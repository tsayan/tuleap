<?php
/**
 * Copyright (c) Enalean, 2023 - present. All Rights Reserved.
 *
 * This file is a part of Tuleap.
 *
 * Tuleap is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Tuleap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Tuleap\Tracker\Tracker\XML\Updater;

use PFUser;
use SimpleXMLElement;
use Tracker;
use Tuleap\Tracker\Action\DuckTypedMoveFieldCollection;
use Tuleap\Tracker\XML\Updater\MoveChangesetXMLUpdater;

final class MoveChangesetXMLDuckTypingUpdater implements UpdateMoveChangesetXMLDuckTyping
{
    public function __construct(
        private readonly MoveChangesetXMLUpdater $move_changeset_XML_updater,
        private readonly UpdateBindValueByDuckTyping $duck_typing_updater,
    ) {
    }

    public function updateFromDuckTypingCollection(
        PFUser $current_user,
        SimpleXMLElement $artifact_xml,
        PFUser $submitted_by,
        int $submitted_on,
        int $moved_time,
        DuckTypedMoveFieldCollection $field_collection,
        Tracker $source_tracker,
    ): void {
        $this->parseChangesetNodesFromDuckTypingCollection(
            $artifact_xml,
            $submitted_by,
            $submitted_on,
            $field_collection
        );

        if (count($artifact_xml->changeset) > 0) {
            $this->move_changeset_XML_updater->addLastMovedChangesetComment(
                $current_user,
                $artifact_xml,
                $source_tracker,
                $moved_time
            );
        }
    }

    private function parseChangesetNodesFromDuckTypingCollection(
        SimpleXMLElement $artifact_xml,
        PFUser $submitted_by,
        int $submitted_on,
        DuckTypedMoveFieldCollection $field_collection,
    ): void {
        $last_index = $artifact_xml->changeset === null ? -1 : count($artifact_xml->changeset) - 1;
        if ($artifact_xml->changeset === null) {
            return;
        }

        $not_migrateable_fields_names = array_map(static fn($field) => $field->getName(), $field_collection->not_migrateable_field_list);
        for ($index = $last_index; $index >= 0; $index--) {
            $this->parseFieldChangeNodesInReverseOrderForDuckTypingCollection(
                $artifact_xml->changeset[$index],
                $field_collection,
                $not_migrateable_fields_names
            );

            if ($this->move_changeset_XML_updater->isChangesetNodeDeletable($artifact_xml, $index)) {
                $this->move_changeset_XML_updater->deleteChangesetNode($artifact_xml, $index);
            }

            if ($index === 0) {
                $this->move_changeset_XML_updater->addSubmittedInformation($artifact_xml->changeset[$index], $submitted_by, $submitted_on);
            }
        }
    }

    /**
     * Parse the SimpleXMLElement field_change nodes to prepare the move action.
     *
     * The parse is done in reverse order to be able to delete a SimpleXMLElement without any issues.
     *
     * @param string[] $not_migrateable_fields_names
     */
    private function parseFieldChangeNodesInReverseOrderForDuckTypingCollection(
        SimpleXMLElement $changeset_xml,
        DuckTypedMoveFieldCollection $field_collection,
        array $not_migrateable_fields_names,
    ): void {
        $this->move_changeset_XML_updater->deleteEmptyCommentsNode($changeset_xml);

        $last_index = $changeset_xml->field_change === null ? -1 : count($changeset_xml->field_change) - 1;
        for ($index = $last_index; $index >= 0; $index--) {
            if (! $changeset_xml->field_change || ! $changeset_xml->field_change[$index]) {
                continue;
            }

            $field_change_name = (string) $changeset_xml->field_change[$index]["field_name"];
            if (in_array($field_change_name, $not_migrateable_fields_names, true)) {
                $this->move_changeset_XML_updater->deleteFieldChangeNode($changeset_xml, $index);
                continue;
            }

            $this->updateFieldChangeNode($field_collection, $changeset_xml, $field_change_name, $index);
        }
    }

    private function updateFieldChangeNode(
        DuckTypedMoveFieldCollection $field_collection,
        SimpleXMLElement $changeset_xml,
        string $field_change_name,
        int $index,
    ): void {
        foreach ($field_collection->migrateable_field_list as $source_field) {
            if ($source_field->getName() !== $field_change_name) {
                continue;
            }

            $target_field = $this->findTargetFieldInFieldsMapping($field_collection, $source_field);
            if (! $target_field) {
                continue;
            }

            if ($source_field instanceof \Tracker_FormElement_Field_List && $target_field instanceof \Tracker_FormElement_Field_List) {
                $this->duck_typing_updater->updateValueForDuckTypingMove($changeset_xml, $source_field, $target_field, $index);
            }

            $this->move_changeset_XML_updater->useTargetTrackerFieldName($changeset_xml, $target_field, $index);
        }
    }

    private function findTargetFieldInFieldsMapping(
        DuckTypedMoveFieldCollection $field_collection,
        \Tracker_FormElement_Field $source_field,
    ): ?\Tracker_FormElement_Field {
        foreach ($field_collection->mapping_fields as $mapping_field) {
            if ($mapping_field->source->getName() === $source_field->getName()) {
                return $mapping_field->destination;
            }
        }

        return null;
    }
}
