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

    public function insert($entity);

    public function update($entity);

    public function delete($entity);
}