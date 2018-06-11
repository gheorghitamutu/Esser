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
    protected $_entityTable = 'AUTOMATEDREPORT';
    protected $_entityClass = 'AutomatedReport';

    /**
     * Create an automatedreport entity with the supplied data
     */
    protected function _createEntity(array $data)
    {
        $automatedreport = array(
            'reportId'      => $data['REPORTID'],
            'reportPath'    => $data['REPORTPATH'],
            'reportType'    => $data['REPORTTYPE'],
            'reportFormat'  => $data['REPORTFORMAT'],
            'rCreatedAt'    => $data['RCREATEDAT']
        );
        return $automatedreport;
    }
}
