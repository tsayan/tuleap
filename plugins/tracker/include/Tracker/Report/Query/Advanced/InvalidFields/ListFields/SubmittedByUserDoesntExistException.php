<?php
/**
 * Copyright (c) Enalean, 2021 - Present. All Rights Reserved.
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
 * along with Tuleap; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

declare(strict_types=1);

namespace Tuleap\Tracker\Report\Query\Advanced\InvalidFields\ListFields;

use Tracker_FormElement_Field;
use Tuleap\Tracker\Report\Query\Advanced\InvalidFields\InvalidFieldException;

final class SubmittedByUserDoesntExistException extends InvalidFieldException
{
    public function __construct(Tracker_FormElement_Field $field, string $value)
    {
        $message = sprintf(
            dgettext("tuleap-tracker", "Error with the field '%s'. The user '%s' does not exist."),
            $field->getName(),
            $value
        );

        parent::__construct($message);
    }
}
