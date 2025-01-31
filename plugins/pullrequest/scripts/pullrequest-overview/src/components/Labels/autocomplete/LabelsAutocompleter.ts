/*
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

import type { Lazybox, LazyboxItem } from "@tuleap/lazybox";
import type { ProjectLabel } from "@tuleap/plugin-pullrequest-rest-api-types";
import type { BuildGroupOfLabels } from "./GroupOfLabelsBuilder";
import { findLabelMatchingValue } from "./LabelFinder";

export interface AutocompleteLabels {
    autocomplete(
        lazybox: Lazybox,
        project_labels: ReadonlyArray<LazyboxItem>,
        currently_selected_labels: ProjectLabel[],
        query: string
    ): void;
}

export const LabelsAutocompleter = (group_builder: BuildGroupOfLabels): AutocompleteLabels => {
    return {
        autocomplete(
            lazybox: Lazybox,
            project_labels: ReadonlyArray<LazyboxItem>,
            currently_selected_labels: ProjectLabel[],
            query: string
        ): void {
            const trimmed_query = query.trim();
            if (trimmed_query === "") {
                lazybox.replaceDropdownContent([group_builder.buildWithLabels(project_labels)]);
                return;
            }

            lazybox.replaceDropdownContent([
                group_builder.buildWithLabels(
                    findLabelMatchingValue(project_labels, trimmed_query)
                ),
            ]);
        },
    };
};
