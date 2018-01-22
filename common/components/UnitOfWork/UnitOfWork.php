<?php

namespace common\components\UnitOfWork;

use Yii;
use yii\helpers\ArrayHelper;
use yii\db\Connection;
use yii\db\ActiveRecord;

use SplObjectStorage;
use Exception;

/**
 * Unit of Work pattern implementation using ActiveRecord
 */
class UnitOfWork implements IUnitOfWork
{
    const STATE_NEW     = 'new';
    const STATE_CLEAN   = 'clean';
    const STATE_CHANGED = 'changed';
    const STATE_DELETED = 'deleted';


    private $storage = [];
    private $relationStorage = null;

    private $transaction = null;


    public function __construct(Connection $db)
    {
        $this->db = $db;
        $this->relationStorage = new SplObjectStorage();
    }

    public function registerNew(ActiveRecord $model)
    {
        $this->registerModel($model, self::STATE_NEW);
        return $this;
    }

    public function registerClean(ActiveRecord $model)
    {
        $this->registerModel($model, self::STATE_CLEAN);
        return $this;
    }

    public function registerChanged(ActiveRecord $model)
    {
        $this->registerModel($model, self::STATE_CHANGED);
        return $this;
    }

    public function registerDeleted(ActiveRecord $model)
    {
        $this->registerModel($model, self::STATE_DELETED);
        return $this;
    }

    protected function registerModel(ActiveRecord $model, $state)
    {
        $this->storage[] = [$state, $model];
    }



    public function registerRelation(ActiveRecord $model, $attribute, ActiveRecord $parentModel, $parentModelAttribute)
    {
        if (isset($this->relationStorage[$model])) {
            $modelRelations = $this->relationStorage[$model];
        } else {
            $modelRelations = [];
        }

        $modelRelations[] = [$attribute, $parentModel, $parentModelAttribute];
        $this->relationStorage[$model] = $modelRelations;
    }

    protected function fillModelRelations(ActiveRecord $model)
    {
        if (!isset($this->relationStorage[$model])) return;

        $modelRelations = $this->relationStorage[$model];
        foreach ($modelRelations as $modelRelation) {
            list($attribute, $parentModel, $parentModelAttribute) = $modelRelation;
            $model->$attribute = $parentModel->$parentModelAttribute;
        }
    }



    public function commit()
    {
        $this->startTransaction();

        try {
            $unitCommitted = true;

            foreach ($this->storage as $storageItem) {
                list($state, $model) = $storageItem;
                $this->fillModelRelations($model);

                $itemCommitted = $this->commitItem($state, $model);

                if (!$itemCommitted) {
                    $unitCommitted = false;
                    break;
                }
            }

            if ($unitCommitted) {
                $this->commitTransaction();
                $this->clear();
            } else {
                $this->rollbackTransaction();
            }

            return $unitCommitted;

        } catch (Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }
    }

    public function rollback()
    {
        $this->rollbackTransaction();
    }

    public function clear()
    {
        $this->transaction = null;
        $this->storage = [];
    }



    protected function commitItem($state, ActiveRecord $model)
    {
        $itemCommitted = true;

        switch ($state) {
            case self::STATE_NEW:
                $itemCommitted = $model->save();
            break;

            case self::STATE_CHANGED:
                $itemCommitted = $model->save();
            break;

            case self::STATE_DELETED:
                $itemCommitted = $model->delete();
            break;

            case self::STATE_CLEAN:
            default:
                // do nothing
            break;
        }

        return $itemCommitted;
    }

    protected function startTransaction()
    {
        $this->transaction = $this->db->beginTransaction();
    }

    protected function commitTransaction()
    {
        if ($this->transaction) {
            $this->transaction->commit();
        }
    }

    protected function rollbackTransaction()
    {
        if ($this->transaction) {
            $this->transaction->rollBack();
        }
    }
}
