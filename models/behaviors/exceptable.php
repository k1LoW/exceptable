<?php
class ExceptableBehavior extends ModelBehavior {

    public $settings = array();

    protected $_defaults = array();

    public function setup(Model $model, $config = array()) {
        $this->settings[$model->alias] = array_merge($this->_defaults, $config);
    }

    /**
     * beforeFind
     *
     * @return
     */
    function beforeFind(Model $model, $queryData){
        if (!empty($queryData['fields'])) {
            return $queryData;
        }
        if (!empty($queryData['except'])) {
            $except = (array)$queryData['except'];
            foreach ($except as $key => $field) {
                if (!preg_match('/^`/', $field)) {
                    $field = '`' . $field . '`';
                    if (strpos($field, '.') !== false) {
                        $field = str_replace('.', '`.`', $field);
                    }
                }
                if (strpos($field, '.') === false) {
                    $field = '`' . $model->alias . '`.' . $field;
                }
                $except[$key] = $field;
            }

            if (!$db =& ConnectionManager::getDataSource($model->useDbConfig)) {
                return false;
            }

            $queryData = $db->__scrubQueryData($queryData);

            $recursive = $model->recursive;
            if ($recursive === null && isset($queryData['recursive'])) {
                $recursive = $queryData['recursive'];
            }

            if (!is_null($recursive)) {
                $_recursive = $model->recursive;
                $model->recursive = $recursive;
            }

            if (!empty($queryData['fields'])) {
                $db->__bypass = true;
                $queryData['fields'] = $db->fields($model, null, $queryData['fields']);
            } else {
                $queryData['fields'] = $db->fields($model);
            }

            $_associations = $model->__associations;

            if ($model->recursive == -1) {
                $_associations = array();
            } else if ($model->recursive == 0) {
                unset($_associations[2], $_associations[3]);
            }

            foreach ($_associations as $type) {
                foreach ($model->{$type} as $assoc => $assocData) {
                    $linkModel =& $model->{$assoc};
                    $external = isset($assocData['external']);
                    if ($model->useDbConfig == $linkModel->useDbConfig) {
                        if (true === $db->generateAssociationQuery($model, $linkModel, $type, $assoc, $assocData, $queryData, $external, $null)) {
                            $linkedModels[$type . '/' . $assoc] = true;
                        }
                    }
                }
            }

            $queryData['fields'] = array_diff($queryData['fields'], $except);
        }

        return $queryData;
    }
}

