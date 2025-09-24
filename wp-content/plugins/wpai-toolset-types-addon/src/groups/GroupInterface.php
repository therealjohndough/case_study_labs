<?php

namespace wpai_toolset_types_add_on\groups;

/**
 * Interface FieldInterface
 * @package wpai_toolset_types_add_on\groups
 */
interface GroupInterface{

    /**
     * @return mixed
     */
    public function initFields();

    /**
     * @return mixed
     */
    public function view();

    /**
     * @param $parsingData
     * @return mixed
     */
    public function parse($parsingData);

    /**
     * @param $importData
     * @return mixed
     */
    public function saved_post($importData);

}