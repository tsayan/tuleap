<?php
/**
 * Copyright (c) Enalean, 2018 - Present. All Rights Reserved.
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

namespace Tuleap\CrossTracker\Report\Query\Advanced\QueryBuilder\Metadata\AlwaysThereField\Date;

use Tracker;
use Tuleap\Tracker\Report\Query\IProvideParametrizedFromAndWhereSQLFragments;
use Tuleap\Tracker\Report\Query\ParametrizedFromWhere;
use Tuleap\Tracker\Report\Query\Advanced\Grammar\Comparison;
use Tuleap\Tracker\Report\Query\Advanced\Grammar\Metadata;
use Tuleap\Tracker\Report\Query\Advanced\QueryBuilder\DateTimeValueRounder;

final class BetweenComparisonFromWhereBuilder implements FromWhereBuilder
{
    /**
     * @var DateTimeValueRounder
     */
    private $date_time_value_rounder;
    /**
     * @var string
     */
    private $alias_field;
    /**
     * @var DateValueExtractor
     */
    private $extractor;

    /**
     * @param string $alias_field
     */
    public function __construct(
        DateValueExtractor $extractor,
        DateTimeValueRounder $date_time_value_rounder,
        $alias_field,
    ) {
        $this->date_time_value_rounder = $date_time_value_rounder;
        $this->alias_field             = $alias_field;
        $this->extractor               = $extractor;
    }

    /**
     * @param Tracker[] $trackers
     * @return IProvideParametrizedFromAndWhereSQLFragments
     */
    public function getFromWhere(Metadata $metadata, Comparison $comparison, array $trackers)
    {
        $value = $this->extractor->getValue($comparison);

        if (! is_array($value)) {
            throw new \Exception("Invalid value for between comparison");
        }

        $min_value = $value['min_value'];
        $max_value = $value['max_value'];

        $min_value_floored_timestamp = $this->date_time_value_rounder->getFlooredTimestampFromDateTime($min_value);
        $max_value_ceiled_timestamp  = $this->date_time_value_rounder->getCeiledTimestampFromDateTime($max_value);

        $where = "{$this->alias_field} >= ? AND {$this->alias_field} <= ?";

        $where_parameters = [$min_value_floored_timestamp, $max_value_ceiled_timestamp];

        return new ParametrizedFromWhere('', $where, [], $where_parameters);
    }
}
