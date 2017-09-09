<?php

class Tag extends Illuminate\Database\Eloquent\Model
{
	protected $fillable = ['name'];
	
    public function articles()
    {
        return $this->belongsToMany('Article');
    }

    public static function process($tags)
    {
        if (empty($tags)) return [];

        $currentTags = array_filter($tags, 'is_numeric');
        $newTags = array_diff($tags, $currentTags);

        foreach ($newTags as $newTag) {
            if ($tag = Tag::create(['name' => $newTag]))
                $currentTags[] = $tag->id;
        }

        return $currentTags;
    }
}