/**
 * Copyright (c) Enalean, 2022 - Present. All Rights Reserved.
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
import type { LogEntry } from "../../api/log-rest-querier";

const getLogs = jest.fn();
jest.mock("../../api/log-rest-querier", () => {
    return {
        getLogs,
    };
});

import { errAsync, okAsync } from "neverthrow";
import HistoryLogs from "./HistoryLogs.vue";
import HistoryLogsLoadingState from "./HistoryLogsLoadingState.vue";
import HistoryLogsErrorState from "./HistoryLogsErrorState.vue";
import HistoryLogsEmptyState from "./HistoryLogsEmptyState.vue";
import HistoryLogsContent from "./HistoryLogsContent.vue";
import { shallowMount } from "@vue/test-utils";
import type { Embedded } from "../../type";
import { getGlobalTestOptions } from "../../helpers/global-options-for-test";
import { nextTick } from "vue";

describe("HistoryLogs", () => {
    it("should display a loading state", () => {
        getLogs.mockReturnValue(okAsync([]));

        const wrapper = shallowMount(HistoryLogs, {
            props: {
                item: { id: 42 } as Embedded,
            },
            global: { ...getGlobalTestOptions({}) },
        });

        expect(wrapper.findComponent(HistoryLogsLoadingState).exists()).toBe(true);
        expect(wrapper.findComponent(HistoryLogsErrorState).exists()).toBe(false);
        expect(wrapper.findComponent(HistoryLogsEmptyState).exists()).toBe(false);
        expect(wrapper.findComponent(HistoryLogsContent).exists()).toBe(false);
    });

    it("should display an empty state", async () => {
        getLogs.mockReturnValue(okAsync([]));

        const wrapper = shallowMount(HistoryLogs, {
            props: {
                item: { id: 42 } as Embedded,
            },
            global: { ...getGlobalTestOptions({}) },
        });

        await nextTick();
        await nextTick();

        expect(wrapper.findComponent(HistoryLogsLoadingState).exists()).toBe(false);
        expect(wrapper.findComponent(HistoryLogsErrorState).exists()).toBe(false);
        expect(wrapper.findComponent(HistoryLogsEmptyState).exists()).toBe(true);
        expect(wrapper.findComponent(HistoryLogsContent).exists()).toBe(false);
    });

    it("should display an error state", async () => {
        getLogs.mockReturnValue(errAsync(Error("You cannot!")));

        const wrapper = shallowMount(HistoryLogs, {
            props: {
                item: { id: 42 } as Embedded,
            },
            global: { ...getGlobalTestOptions({}) },
        });

        await nextTick();
        await nextTick();

        expect(wrapper.findComponent(HistoryLogsLoadingState).exists()).toBe(false);
        expect(wrapper.findComponent(HistoryLogsErrorState).exists()).toBe(true);
        expect(wrapper.findComponent(HistoryLogsEmptyState).exists()).toBe(false);
        expect(wrapper.findComponent(HistoryLogsContent).exists()).toBe(false);
    });

    it("should display content", async () => {
        getLogs.mockReturnValue(okAsync([{} as LogEntry]));

        const wrapper = shallowMount(HistoryLogs, {
            props: {
                item: { id: 42 } as Embedded,
            },
            global: { ...getGlobalTestOptions({}) },
        });

        await nextTick();
        await nextTick();

        expect(wrapper.findComponent(HistoryLogsLoadingState).exists()).toBe(false);
        expect(wrapper.findComponent(HistoryLogsErrorState).exists()).toBe(false);
        expect(wrapper.findComponent(HistoryLogsEmptyState).exists()).toBe(false);
        expect(wrapper.findComponent(HistoryLogsContent).exists()).toBe(true);
    });
});
