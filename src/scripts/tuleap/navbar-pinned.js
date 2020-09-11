/*
 * Copyright (c) Enalean, 2020 - Present. All Rights Reserved.
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

export function init() {
    const header = document.querySelector("header");
    if (!header) {
        return;
    }

    let ticking = false;

    if (document.body.classList.contains("has-sidebar")) {
        return;
    }

    handlePinnedHeader(header);
    window.addEventListener("scroll", () => {
        if (!ticking) {
            window.requestAnimationFrame(() => {
                handlePinnedHeader(header);
                ticking = false;
            });
        }
        ticking = true;
    });
}

function handlePinnedHeader(header) {
    const scroll_coordinates = {
        x: window.pageXOffset,
        y: window.pageYOffset,
    };

    if (scroll_coordinates.y > 35) {
        header.classList.add("pinned");
    } else {
        header.classList.remove("pinned");
    }
}
