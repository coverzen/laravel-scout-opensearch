<?php declare(strict_types=1);

namespace Zing\LaravelScout\OpenSearch\Trait;

use Illuminate\Support\Collection as BaseCollection;
use Laravel\Scout\Searchable as ScoutSearchable;

trait OpenSearchSearchable
{
    use ScoutSearchable;

    public function registerSearchableMacros()
    {
        $self = $this;

        BaseCollection::macro('searchable', function () use ($self) {
            $self->queueMakeSearchable($this);
        });

        BaseCollection::macro('unsearchable', function () use ($self) {
            $self->queueRemoveFromSearch($this);
        });

        BaseCollection::macro('searchableSync', function ($options = []) use ($self) {
            $self->syncMakeSearchable($this, $options);
        });

        BaseCollection::macro('unsearchableSync', function ($options = []) use ($self) {
            $self->syncRemoveFromSearch($this, $options);
        });
    }

    public function syncMakeSearchable($models, $options = [])
    {
        if ($models->isEmpty()) {
            return;
        }

        $searchableUsing = $models->first()->makeSearchableUsing($models)->first()->searchableUsing();

        if(method_exists($searchableUsing, "updateWithOptions")){
            return $searchableUsing->updateWithOptions($models, $options);
        } else {
            return $searchableUsing->update($models);
        }
    }

    public function syncRemoveFromSearch($models, $options = [])
    {
        if ($models->isEmpty()) {
            return;
        }

        $searchableUsing = $models->first()->searchableUsing();

        if(method_exists($searchableUsing, "deleteWithOptions")){
            return $searchableUsing->deleteWithOptions($models, $options);
        } else {
            return $searchableUsing->delete($models);
        }
    }

    public function searchableSync($options = [])
    {
        $this->newCollection([$this])->searchableSync($options);
    }

    public function unsearchableSync($options = [])
    {
        $this->newCollection([$this])->unsearchableSync($options);
    }

}
