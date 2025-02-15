<?php

namespace App\Service;

use App\Entity\DownloadCounter;
use App\Entity\Gamification;
use App\Entity\ScoreHistory;
use App\Entity\Song;
use App\Entity\Utilisateur;
use App\Entity\Vote;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\VarDumper\VarDumper;

class GamificationService
{
    private static $gamification;
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param int $achievement
     * @param Utilisateur $user
     */
    public function unlock(int $achievement, Utilisateur $user): void
    {
        if ($this->get($achievement, $user)) {
            return;
        }
        $gamification = $this->em->getRepository(Gamification::class)->findOneBy([
            'achievement' => $achievement,
            'user' => $user
        ]);
        if ($gamification == null) {
            $gamification = new Gamification();
            $gamification->setAchievement($achievement);
            $gamification->setUser($user);
            $gamification->setAchievementNeeds(1);
            $gamification->setAchievementCount(1);
            $gamification->setAchievementUnicity(serialize([]));
            $this->em->persist($gamification);
            $this->em->flush();
        }
    }

    /**
     * @param int $achievement
     * @param Utilisateur $user
     * @param int $add
     * @param int $need
     */
    public function add(int $achievement, Utilisateur $user, int $add, int $need, string $uniq): void
    {
        /** @var Gamification $gamification */
        $gamification = $this->em->getRepository(Gamification::class)->findOneBy([
            'achievement' => $achievement,
            'user' => $user
        ]);
        if ($gamification == null) {
            $gamification = new Gamification();
            $gamification->setAchievement($achievement);
            $gamification->setUser($user);
            $gamification->setAchievementNeeds($need);
            $gamification->setAchievementUnicity(serialize([]));
            $this->em->persist($gamification);
        }
        $unicity = unserialize($gamification->getAchievementUnicity());
        if (!in_array($uniq, $unicity)) {
            $gamification->setAchievementCount($gamification->getAchievementCount() + $add);
            $unicity[] = $uniq;
            $gamification->setAchievementUnicity(serialize($unicity));
        }
        $this->em->flush();

    }

    public function count($user)
    {
        $this->getByUser($user);
        $count = 0;
        if(self::$gamification == null )return 0;
        foreach (self::$gamification as $r) {
            $count += $r ? 1 : 0;
        }
        return $count;
    }

    /**
     * @param Utilisateur $user
     * @return bool[]
     */
    public function getByUser(Utilisateur $user): void
    {
        if (self::$gamification == null) {
            /** @var Gamification[] $gamifications */
            $gamifications = $this->em->getRepository(Gamification::class)->findBy([
                'user' => $user
            ]);
            foreach ($gamifications as $gamification) {
                self::$gamification[$gamification->getAchievement()] = $gamification->getAchievementCount() >= $gamification->getAchievementNeeds();
            }
        }
    }

    /**
     * @param int $achievement
     * @param Utilisateur $user
     * @return bool
     */
    public function get(int $achievement, Utilisateur $user): bool
    {
        $this->getByUser($user);
        return isset(self::$gamification[$achievement]) && self::$gamification[$achievement];
    }

    public function reset()
    {
        self::$gamification = null;
    }
}

