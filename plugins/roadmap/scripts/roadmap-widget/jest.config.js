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

const path = require("path");
const { defineJestConfiguration } = require("@tuleap/build-system-configurator");

process.env.DISABLE_TS_TYPECHECK = "true";

const configuration = defineJestConfiguration();

module.exports = {
    ...configuration,
    displayName: "roadmap",
    transform: {
        ...configuration.transform,
        "^.+\\.vue$": "unplugin-vue2-script-setup/jest",
    },
    moduleNameMapper: {
        ...configuration.moduleNameMapper,
        "^vue$": path.resolve(__dirname, "./node_modules/vue/"),
    },
};
