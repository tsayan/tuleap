<?php
/**
 * Copyright (c) Enalean, 2021-Present. All Rights Reserved.
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

namespace Tuleap\ProgramManagement\Adapter\Program\Backlog\ProgramIncrement\Source\Changeset\Values;

use Tuleap\ProgramManagement\Domain\Program\Backlog\ProgramIncrement\Source\Changeset\Values\BindValueLabel;
use Tuleap\ProgramManagement\Domain\Program\Backlog\ProgramIncrement\Source\Changeset\Values\ChangesetValueNotFoundException;
use Tuleap\ProgramManagement\Domain\Program\Backlog\ProgramIncrement\Source\Changeset\Values\UnsupportedTitleFieldException;
use Tuleap\ProgramManagement\Domain\Program\Backlog\ProgramIncrement\Source\Fields\SynchronizedFields;
use Tuleap\ProgramManagement\Tests\Builder\SynchronizedFieldsBuilder;

final class ArtifactFieldValuesRetrieverTest extends \Tuleap\Test\PHPUnit\TestCase
{
    private SynchronizedFields $fields;
    /**
     * @var \PHPUnit\Framework\MockObject\Stub&\Tracker_Artifact_Changeset
     */
    private $changeset;
    /**
     * @var \PHPUnit\Framework\MockObject\Stub&\Tracker_FormElementFactory
     */
    private $form_element_factory;
    private \Tracker_FormElement_Field_String $title_field;
    private \Tracker_FormElement_Field_Text $description_field;
    private \Tracker_FormElement_Field_Selectbox $status_field;
    private \Tracker_FormElement_Field_Date $start_date_field;
    private \Tracker_FormElement_Field_Date $end_period_field;

    protected function setUp(): void
    {
        $this->title_field       = new \Tracker_FormElement_Field_String(1376, 89, 1000, 'title', 'Title', 'Irrelevant', true, 'P', true, '', 2);
        $this->description_field = new \Tracker_FormElement_Field_Text(
            1412,
            89,
            1000,
            'description',
            'Description',
            'Irrelevant',
            true,
            'P',
            false,
            '',
            3
        );
        $this->status_field      = new \Tracker_FormElement_Field_Selectbox(1499, 89, 1000, 'status', 'Status', 'Irrelevant', true, 'P', false, '', 4);
        $this->start_date_field  = new \Tracker_FormElement_Field_Date(1784, 89, 1000, 'date', 'Date', 'Irrelevant', true, 'P', false, '', 5);
        $this->end_period_field  = new \Tracker_FormElement_Field_Date(1368, 89, 1000, 'date', 'Date', 'Irrelevant', true, 'P', false, '', 6);

        $this->form_element_factory = $this->createStub(\Tracker_FormElementFactory::class);
        $this->fields               = SynchronizedFieldsBuilder::buildWithFields(
            $this->title_field,
            $this->description_field,
            $this->status_field,
            $this->start_date_field,
            $this->end_period_field
        );
        $this->changeset            = $this->createMock(\Tracker_Artifact_Changeset::class);
    }

    private function getRetrieverWithFactory(\Tracker_Artifact_Changeset $changeset, \Tracker_FormElementFactory $form_element_factory): ArtifactFieldValuesRetriever
    {
        return new ArtifactFieldValuesRetriever($changeset, $form_element_factory);
    }

    private function getRetriever(\Tracker_Artifact_Changeset $changeset): ArtifactFieldValuesRetriever
    {
        return new ArtifactFieldValuesRetriever($changeset, $this->form_element_factory);
    }

    public function dataProviderMethodUnderTest(): array
    {
        return [
            'when title value is not found'       => [fn(
                \Tracker_Artifact_Changeset $changeset,
                SynchronizedFields $fields,
                \Tracker_FormElementFactory $form_element_factory
            ) => $this->getRetrieverWithFactory($changeset, $form_element_factory)->getTitleValue($fields)],
            'when description value is not found' => [fn(
                \Tracker_Artifact_Changeset $changeset,
                SynchronizedFields $fields,
                \Tracker_FormElementFactory $form_element_factory
            ) => $this->getRetrieverWithFactory($changeset, $form_element_factory)->getDescriptionValue($fields)],
            'when start date value is not found'  => [fn(
                \Tracker_Artifact_Changeset $changeset,
                SynchronizedFields $fields,
                \Tracker_FormElementFactory $form_element_factory
            ) => $this->getRetrieverWithFactory($changeset, $form_element_factory)->getStartDateValue($fields)],
            'when end period value is not found'  => [fn(
                \Tracker_Artifact_Changeset $changeset,
                SynchronizedFields $fields,
                \Tracker_FormElementFactory $form_element_factory
            ) => $this->getRetrieverWithFactory($changeset, $form_element_factory)->getEndPeriodValue($fields)],
            'when status value is not found'      => [fn(
                \Tracker_Artifact_Changeset $changeset,
                SynchronizedFields $fields,
                \Tracker_FormElementFactory $form_element_factory
            ) => $this->getRetrieverWithFactory($changeset, $form_element_factory)->getStatusValues($fields)]
        ];
    }

    /**
     * @dataProvider dataProviderMethodUnderTest
     */
    public function testItThrowsWhenChangesetValuesAreNotFound(callable $method_under_test): void
    {
        $this->changeset->method('getValue')->willReturn(null);
        $this->changeset->method('getId')->willReturn(1);

        $this->form_element_factory->method('getFieldById')->willReturn($this->createStub(\Tracker_FormElement_Field::class));

        $this->expectException(ChangesetValueNotFoundException::class);
        $method_under_test($this->changeset, $this->fields, $this->form_element_factory);
    }

    public function testItThrowsWhenTitleIsNotAString(): void
    {
        $changeset_value = $this->createStub(\Tracker_Artifact_ChangesetValue_Text::class);
        $this->changeset->method('getValue')->willReturn($changeset_value);

        $this->form_element_factory->method('getFieldById')->willReturn($this->title_field);

        $this->expectException(UnsupportedTitleFieldException::class);
        $this->getRetriever($this->changeset)->getTitleValue($this->fields);
    }

    public function testItReturnsTitleValue(): void
    {
        $fields          = SynchronizedFieldsBuilder::build();
        $changeset_value = $this->createStub(\Tracker_Artifact_ChangesetValue_String::class);
        $changeset_value->method('getValue')->willReturn('My title');
        $this->changeset->method('getValue')->willReturn($changeset_value);

        $this->form_element_factory->method('getFieldById')->willReturn($this->title_field);

        self::assertSame('My title', $this->getRetriever($this->changeset)->getTitleValue($fields));
    }

    public function testItReturnsDescriptionValue(): void
    {
        $changeset_value = $this->createStub(\Tracker_Artifact_ChangesetValue_Text::class);
        $changeset_value->method('getValue')->willReturn('My description');
        $changeset_value->method('getFormat')->willReturn('text');
        $this->changeset->method('getValue')->willReturn($changeset_value);

        $this->form_element_factory->method('getFieldById')->willReturn($this->description_field);

        $text_value = $this->getRetriever($this->changeset)->getDescriptionValue($this->fields);
        self::assertSame('My description', $text_value->getValue());
        self::assertSame('text', $text_value->getFormat());
    }

    public function testItReturnsStartDateValue(): void
    {
        $changeset_value = $this->createStub(\Tracker_Artifact_ChangesetValue_Date::class);
        $changeset_value->method('getDate')->willReturn('2020-10-01');
        $this->changeset->method('getValue')->willReturn($changeset_value);

        $this->form_element_factory->method('getFieldById')->willReturn($this->start_date_field);

        self::assertSame('2020-10-01', $this->getRetriever($this->changeset)->getStartDateValue($this->fields));
    }

    public function testItReturnsEndPeriodValueWithEndDate(): void
    {
        $changeset_value = $this->createStub(\Tracker_Artifact_ChangesetValue_Date::class);
        $changeset_value->method('getValue')->willReturn('2023-09-01');
        $this->changeset->method('getValue')->willReturn($changeset_value);

        $this->form_element_factory->method('getFieldById')->willReturn($this->end_period_field);

        self::assertSame('2023-09-01', $this->getRetriever($this->changeset)->getEndPeriodValue($this->fields));
    }

    public function testItReturnsEndPeriodValueWithDuration(): void
    {
        $changeset_value = $this->createStub(\Tracker_Artifact_ChangesetValue_Integer::class);
        $changeset_value->method('getValue')->willReturn(34);
        $this->changeset->method('getValue')->willReturn($changeset_value);

        $this->form_element_factory->method('getFieldById')->willReturn($this->end_period_field);

        self::assertSame('34', $this->getRetriever($this->changeset)->getEndPeriodValue($this->fields));
    }

    public function testItReturnsStatusValuesWithStaticBind(): void
    {
        $first_bind_value  = new \Tracker_FormElement_Field_List_Bind_StaticValue(557, 'Planned', '', 0, false);
        $second_bind_value = new \Tracker_FormElement_Field_List_Bind_StaticValue(698, 'Current', '', 1, false);
        $changeset_value   = $this->createStub(\Tracker_Artifact_ChangesetValue_List::class);
        $changeset_value->method('getListValues')->willReturn([$first_bind_value, $second_bind_value]);
        $this->changeset->method('getValue')->willReturn($changeset_value);

        $this->form_element_factory->method('getFieldById')->willReturn($this->status_field);

        $values = $this->getRetriever($this->changeset)->getStatusValues($this->fields);
        $labels = array_map(static fn(BindValueLabel $label): string => $label->getLabel(), $values);
        self::assertContains('Planned', $labels);
        self::assertContains('Current', $labels);
    }

    public function testItReturnsStatusValuesWithUsersBind(): void
    {
        $first_bind_value  = new \Tracker_FormElement_Field_List_Bind_UsersValue(138, 'mgregg', 'Meridith Gregg');
        $second_bind_value = new \Tracker_FormElement_Field_List_Bind_UsersValue(129, 'mmantel', 'Mildred Mantel');
        $changeset_value   = $this->createStub(\Tracker_Artifact_ChangesetValue_List::class);
        $changeset_value->method('getListValues')->willReturn([$first_bind_value, $second_bind_value]);
        $this->changeset->method('getValue')->willReturn($changeset_value);

        $this->form_element_factory->method('getFieldById')->willReturn($this->status_field);

        $values = $this->getRetriever($this->changeset)->getStatusValues($this->fields);
        $labels = array_map(static fn(BindValueLabel $label): string => $label->getLabel(), $values);
        self::assertContains('Meridith Gregg', $labels);
        self::assertContains('Mildred Mantel', $labels);
    }

    public function testItReturnsStatusValuesWithUserGroupsBind(): void
    {
        $first_ugroup      = new \ProjectUGroup([
            'ugroup_id' => \ProjectUGroup::PROJECT_MEMBERS,
            'name'      => \ProjectUGroup::NORMALIZED_NAMES[\ProjectUGroup::PROJECT_MEMBERS],
        ]);
        $first_bind_value  = new \Tracker_FormElement_Field_List_Bind_UgroupsValue(95, $first_ugroup, false);
        $second_ugroup     = new \ProjectUGroup([
            'ugroup_id' => 351,
            'name'      => 'bicyanide benzothiopyran',
        ]);
        $second_bind_value = new \Tracker_FormElement_Field_List_Bind_UgroupsValue(265, $second_ugroup, false);
        $changeset_value   = $this->createStub(\Tracker_Artifact_ChangesetValue_List::class);
        $changeset_value->method('getListValues')->willReturn([$first_bind_value, $second_bind_value]);
        $this->changeset->method('getValue')->willReturn($changeset_value);

        $this->form_element_factory->method('getFieldById')->willReturn($this->status_field);

        $values = $this->getRetriever($this->changeset)->getStatusValues($this->fields);
        $labels = array_map(static fn(BindValueLabel $label): string => $label->getLabel(), $values);
        self::assertContains('project_members', $labels);
        self::assertContains('bicyanide benzothiopyran', $labels);
    }
}
