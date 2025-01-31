/**
 * Copyright (c) Enalean, 2021 - Present. All Rights Reserved.
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

import { createLocalVue } from "@vue/test-utils";
import { initVueGettextFromPoGettextPlugin } from "@tuleap/vue2-gettext-init";
import VueDOMPurifyHTML from "vue-dompurify-html";

export async function createProjectLabeledItemsLocalVue() {
    const local_vue = createLocalVue();
    await initVueGettextFromPoGettextPlugin(local_vue, () => {
        throw new Error("Fallback to default");
    });
    local_vue.use(VueDOMPurifyHTML);

    return local_vue;
}
