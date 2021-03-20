<?php

namespace App\Models;

class VacancyLevel
{
    private $remainingCount;

    public function __construct(int $remainingCount)
    {
        $this->remainingCount = $remainingCount;
    }

    public function mark(): string
    {
        $marks = ['empty' => '×', 'few' => '△', 'enough' => '◎'];
        $slug = $this->slug();

        //issetで$marksに値が存在するか確認し、assertで存在しなければ例外処理をする
        assert(isset($marks[$slug]), new \DomainException('invaild slug value'));
        
        return $marks[$slug];
    }

    public function slug(): string
    {
        if ($this->remainingCount === 0) {
            return 'empty';
        }
        if ($this->remainingCount < 5) {
            return 'few';
        }
        return 'enough';
    }

    public function __toString()
    {
        return $this->mark();
    }
}