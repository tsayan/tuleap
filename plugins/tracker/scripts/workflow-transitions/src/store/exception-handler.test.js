/*
 * Copyright (c) Enalean, 2018-Present. All Rights Reserved.
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

import { getErrorMessage } from "./exception-handler.js";

describe("Store fail actions:", () => {
    describe("getErrorMessage()", () => {
        describe("with no error in exception", () => {
            let result;

            beforeEach(async () => {
                const exception = {
                    response: {
                        json: function () {
                            return Promise.resolve({});
                        },
                    },
                };

                result = await getErrorMessage(exception);
            });

            it("returns nothing", () => expect(result).toBeNull());
        });

        describe("with non internationalized exception", () => {
            let result;

            beforeEach(async () => {
                const exception = {
                    response: {
                        json: function () {
                            return Promise.resolve({
                                error: { message: "non internationalized" },
                            });
                        },
                    },
                };

                result = await getErrorMessage(exception);
            });

            it("returns a non internationalized message", () =>
                expect(result).toBe("non internationalized"));
        });

        describe("with internationalized exception", () => {
            let result;

            beforeEach(async () => {
                const exception = {
                    response: {
                        json: function () {
                            return Promise.resolve({
                                error: {
                                    i18n_error_message: "internationalized message",
                                },
                            });
                        },
                    },
                };

                result = await getErrorMessage(exception);
            });

            it("returns an internationalized message", () =>
                expect(result).toBe("internationalized message"));
        });
    });
});
