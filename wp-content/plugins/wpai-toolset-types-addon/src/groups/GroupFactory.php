<?php

namespace wpai_toolset_types_add_on\groups;

/**
 * Class GroupFactory
 * @package wpai_toolset_types_add_on\groups
 */
final class GroupFactory {

    /**
     * @param $groupData
     * @param $post
     * @return GroupInterface
     */
    public static function create($groupData, $post = []) {
        return new Group($groupData, $post);
    }

}