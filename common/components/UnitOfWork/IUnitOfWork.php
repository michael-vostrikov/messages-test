<?php

namespace common\components\UnitOfWork;

use yii\db\ActiveRecord;

/**
 * Unit of Work pattern interface using ActiveRecord
 */
interface IUnitOfWork
{
    public function registerNew(ActiveRecord $model);
    public function registerClean(ActiveRecord $model);
    public function registerChanged(ActiveRecord $model);
    public function registerDeleted(ActiveRecord $model);
    public function registerRelation(ActiveRecord $model, $attribute, ActiveRecord $parentModel, $parentModelAttribute);
    public function commit();
    public function rollback();
    public function clear();
}
