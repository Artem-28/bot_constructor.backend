<?php

namespace App\Transformers;
use App\Models\GroupQuestion;
use League\Fractal\TransformerAbstract;


class GroupQuestionTransformer extends TransformerAbstract
{

    protected array $defaultIncludes = [];

    public function __construct(...$relations)
    {
        $this->defaultIncludes = [...$this->defaultIncludes, ...$relations];
    }

    public function transform(GroupQuestion $group): array
    {
        return [
            'id' => $group->id,
            'prevGroupId' => $group->prev_id,
            'nextGroupId' => $group->next_id,
        ];
    }

    public function includeQuestions(GroupQuestion $group)
    {
        return $this->collection($group->questions, new QuestionTransformer());
    }

    public function includeSortedQuestions(GroupQuestion $group)
    {
        return $this->collection($group->sortedQuestions, new QuestionTransformer());
    }
}
