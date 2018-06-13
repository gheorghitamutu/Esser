<?php
/**
 * Created by PhpStorm.
 * User: Fr33LaNc3
 * Date: 08-Jun-18
 * Time: 18:34
 */

namespace ModelMapper;
use AppModel\Useracc;
use DatabaseConnectivity;

class AutomatedReportMapper extends AbstractMapper
{
    protected $_entityTable = 'AUTOMATEDREPORTS';
    protected $_entityClass = 'AutomatedReport';

    public function __construct(DatabaseConnectivity\DatabaseAdapterInterface $adapter)
    {
        parent::__construct($adapter, array(
            'entityTable' => $this->_entityTable,
            'entityClass' => $this->_entityClass
        ));
    }

    /**
     * Create an automatedreport entity with the supplied data
     */
    protected function _createEntity(array $data)
    {
        $automatedreport = array(
            'reportId'      => array_key_exists ('REPORTID',$data) ? $data['REPORTID'] : '',
            'reportPath'    => array_key_exists ('REPORTPATH',$data) ? $data['REPORTPATH'] : '',
            'reportType'    => array_key_exists ('REPORTTYPE',$data) ? $data['REPORTTYPE'] : '',
            'reportFormat'  => array_key_exists ('REPORTFORMAT',$data) ? $data['REPORTFORMAT'] : '',
            'rCreatedAt'    => array_key_exists ('RCREATEDAT',$data) ? $data['RCREATEDAT'] : ''
        );
        return $automatedreport;
    }
}
