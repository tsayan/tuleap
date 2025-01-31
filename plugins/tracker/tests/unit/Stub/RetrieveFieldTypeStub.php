<?php
/**
 * Copyright (c) Enalean 2023 - Present. All Rights Reserved.
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

namespace Tuleap\Tracker\Test\Stub;

use Tracker_FormElement;
use Tuleap\Tracker\FormElement\RetrieveFieldType;

final class RetrieveFieldTypeStub implements RetrieveFieldType
{
    private const NO_TYPE = "notype";

    private function __construct(private string $type)
    {
    }

    public static function withType(string $type): self
    {
        return new self($type);
    }

    public static function withNoType(): self
    {
        return new self(self::NO_TYPE);
    }

    public function getType(Tracker_FormElement $form_element): string
    {
        if ($this->type === self::NO_TYPE) {
            throw new \RuntimeException("getType was called while the stub RetrieveFieldTypeStub is not configured to return a specific type.");
        }

        return $this->type;
    }
}
