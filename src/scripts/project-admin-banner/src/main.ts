/*
 * Copyright (c) Enalean, 2019-Present. All Rights Reserved.
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

import Vue from "vue";
import { getPOFileFromLocale, initVueGettextFromPoGettextPlugin } from "@tuleap/vue2-gettext-init";
import App from "./components/App.vue";

document.addEventListener("DOMContentLoaded", async () => {
    const vue_mount_point = document.getElementById("banner-admin");
    if (!vue_mount_point) {
        return;
    }

    if (!vue_mount_point.dataset.projectId) {
        return;
    }

    await initVueGettextFromPoGettextPlugin(
        Vue,
        (locale) =>
            import(
                /* webpackChunkName: "project-admin-banner-po-" */ `../po/${getPOFileFromLocale(
                    locale
                )}`
            )
    );

    const AppComponent = Vue.extend(App);
    new AppComponent({
        propsData: {
            message: vue_mount_point.dataset.bannerMessage || "",
            project_id: parseInt(vue_mount_point.dataset.projectId, 10),
            location: window.location,
        },
    }).$mount(vue_mount_point);
});
