<?php

namespace App\Traits;

use App\Models\User;

trait AvatarTrait
{
  private function getAvatarsForMale()
  {
    return [
      'ant-man.svg',
      'art-man.svg',
      'batman.svg',
      'black-flash.svg',
      'bounty-hunter.svg',
      'boy.svg',
      'captain-america.svg',
      'cyclops.svg',
      'darth-sidious.svg',
      'deadpool.svg',
      'dracula.svg',
      'droid.svg',
      'evil-batman.svg',
      'flash.svg',
      'frankenstein.svg',
      'frodo.svg',
      'gandalf.svg',
      'groot.svg',
      'harry-potter.svg',
      'hawkeye.svg',
      'hell-boy.svg',
      'helmet.svg',
      'iron-man.svg',
      'joker.svg',
      'legolas.svg',
      'magento.svg',
      'minion-1.svg',
      'minion-2.svg',
      'minion-3.svg',
      'robot.svg',
      'sonic.svg',
      'space-suit.svg',
      'spiderman.svg',
      'superman.svg',
      'thanos.svg',
      'thief.svg',
      'thor.svg',
      'villain.svg',
      'yoda.svg',
    ];
  }

  private function getAvatarsForFemale()
  {
    return [
      'ariel.svg',
      'cinderella.svg',
      'cyclops-girl.svg',
      'elf-girl.svg',
      'geek-girl.svg',
      'girl.svg',
      'hipster.svg',
      'jasmine.svg',
      'lady.svg',
      'princess.svg',
      'queen.svg',
      'school-girl.svg',
      'super-girl.svg',
      'woman.svg',
      'woman-2.svg',
      'wonder-woman.svg',
    ];
  }

  private function generateAvatarFile($gender)
  {
    $possibleList =
      User::GENDER_FEMALE == $gender
        ? $this->getAvatarsForFemale()
        : $this->getAvatarsForMale();

    return $possibleList[array_rand($possibleList)];
  }

  private function generateAvatarUrl($gender)
  {
    return '/storage/avatars/default/' . $this->generateAvatarFile($gender);
  }
}
