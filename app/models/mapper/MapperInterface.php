<?php
/**
 * Created by PhpStorm.
 * User: Fr33LaNc3
 * Date: 08-Jun-18
 * Time: 17:01
 */

namespace ModelMapper;

interface MapperInterface
{
    public function findById($id);

    public function findAll($criteria = '');

    public function insert($table, array $fieldsandvalues);

    public function update($table, array $fieldsandvalues, $criteria);

    public function delete($table, $criteria);
}
