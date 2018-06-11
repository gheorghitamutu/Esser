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
            'reportId'      => $data['REPORTID'] ? $data['REPORTID'] : '',
            'reportPath'    => $data['REPORTPATH'] ? $data['REPORTPATH'] : '',
            'reportType'    => $data['REPORTTYPE'] ? $data['REPORTTYPE'] : '',
            'reportFormat'  => $data['REPORTFORMAT'] ? $data['REPORTFORMAT'] : '',
            'rCreatedAt'    => $data['RCREATEDAT'] ? $data['RCREATEDAT'] : ''
        );
        return $automatedreport;
    }
}
