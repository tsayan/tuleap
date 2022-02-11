/**
 * Copyright (c) Enalean, 2022 - present. All Rights Reserved.
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

import { getRouterQueryFromSearchParams } from "./get-router-query-from-search-params";
import { buildAdvancedSearchParams } from "./build-advanced-search-params";
import type { AdvancedSearchParams } from "../type";
import type { Dictionary } from "vue-router/types/router";

describe("getRouterQueryFromSearchParams", () => {
    it("should omit empty params to not clutter the query url", () => {
        // We don't use the helper buildAdvancedSearchParams() on purpose:
        // That way, there is a better chance that contributor that is adding
        // a new query parameter do not forget to update isQueryEmpty().
        // It is not bullet proof but we hope that forcing them to touch this
        // test file will help.
        const query_params: AdvancedSearchParams = {
            query: "",
            type: "",
            title: "",
            description: "",
            owner: "",
        };
        expect(getRouterQueryFromSearchParams(query_params)).toStrictEqual({});
    });

    it.each<[Partial<AdvancedSearchParams>, Dictionary<string>]>([
        [{ query: "lorem" }, { q: "lorem" }],
        [{ type: "folder" }, { type: "folder" }],
        [
            { query: "lorem", type: "folder" },
            { q: "lorem", type: "folder" },
        ],
        [{ title: "lorem" }, { title: "lorem" }],
        [{ description: "lorem" }, { description: "lorem" }],
    ])("should return the url parameters based from search parameters", (params, expected) => {
        expect(getRouterQueryFromSearchParams(buildAdvancedSearchParams(params))).toStrictEqual(
            expected
        );
    });

    it("should return the owner parameter", () => {
        expect(
            getRouterQueryFromSearchParams(buildAdvancedSearchParams({ owner: "lorem" }))
        ).toStrictEqual({ owner: "lorem" });
    });
});
