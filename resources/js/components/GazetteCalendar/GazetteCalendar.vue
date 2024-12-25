<script>
import FullCalendar from "@fullcalendar/vue3";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";
import { format } from "date-fns";
import { Toaster, toast } from "vue-sonner";
import api from "./../../../api";

export default {
  components: {
    FullCalendar,
    Toaster,
  },

  data() {
    return {
      selectedHolidays: [],
      calendarOptions: {
        plugins: [dayGridPlugin, interactionPlugin],
        initialView: "dayGridMonth",
        themeSystem: "bootstrap",
        headerToolbar: {
          left: "prev,next today",
          center: "title",
          right: "dayGridMonth",
        },
        selectable: true,
        selectMirror: true,
        dayMaxEvents: true,
        weekends: true,
        events: [],
        dateClick: this.handleDateClick,
        eventClick: this.handleEventClick,
        eventContent: this.renderEventContent,
        eventClassNames: "holiday-event",
        buttonText: {
          today: "Today",
        },
        eventColor: "#007bff",
        eventDisplay: "block",
        height: "auto",
        bootstrapFontAwesome: {
          prev: "fa-chevron-left",
          next: "fa-chevron-right",
        },
      },
    };
  },

  methods: {
    handleDateClick(info) {
      const clickedDate = info.dateStr;
      const existingHoliday = this.selectedHolidays.find((h) => h.date === clickedDate);

      if (!existingHoliday) {
        const newHoliday = {
          id: Date.now(),
          date: clickedDate,
          title: "Holiday",
          allDay: true,
        };
        this.selectedHolidays.push(newHoliday);
        this.updateCalendarEvents();
      }
    },

    handleEventClick(info) {
      if (confirm("Remove this holiday?")) {
        const holidayToRemove = this.selectedHolidays.find((h) => h.date === info.event.startStr);
        if (holidayToRemove) {
          this.removeHoliday(holidayToRemove);
        }
      }
    },

    removeHoliday(holiday) {
      const index = this.selectedHolidays.findIndex((h) => h.id === holiday.id);
      if (index !== -1) {
        this.selectedHolidays.splice(index, 1);
        this.updateCalendarEvents();
        toast.info("Holiday removed");
      }
    },

    updateCalendarEvents() {
      const calendarApi = this.$refs.fullCalendar.getApi();
      calendarApi.removeAllEvents();

      const events = this.selectedHolidays.map((holiday) => ({
        id: holiday.id.toString(),
        title: holiday.title,
        start: holiday.date,
        allDay: true,
        className: "holiday-event",
      }));

      calendarApi.addEventSource(events);
    },

    renderEventContent(eventInfo) {
      return {
        html: `<div class="fc-event-main-inner">
          <div class="holiday-title">${eventInfo.event.title}</div>
        </div>`,
      };
    },

    formatDate(dateStr) {
      return format(new Date(dateStr), "MMMM d, yyyy");
    },

    async loadExistingHolidays() {
      try {
        const response = await api.get(`/api/gazette-holidays`, {
          params: {
            year: new Date().getFullYear(),
            month: new Date().getMonth() + 1,
          },
        });

        this.selectedHolidays = response.data.map((holiday) => ({
          id: Date.now() + Math.random(),
          date: holiday.date,
          title: "Holiday",
          allDay: true,
        }));

        this.updateCalendarEvents();
      } catch (error) {
        toast.error("Error loading holidays");
      }
    },

    async saveHolidays() {
      try {
        const holidays = this.selectedHolidays.map((holiday) => ({
          date: holiday.date,
        }));

        await api.post(`/api/gazette-holidays`, { holidays });
        toast.success("Holiday saved successfully");
      } catch (error) {
        console.error(error);
        toast.error("Error saving holidays");
      }
    },
  },

  mounted() {
    this.loadExistingHolidays();
  },
};
</script>

<template>
  <div class="gazette-calendar">
    <div class="card">
      <Toaster richColors position="top-right" />

      <div class="card-body">
        <FullCalendar
          ref="fullCalendar"
          :options="calendarOptions"
          class="gazette-calendar-fc mb-4"
        />

        <div class="selected-holidays mt-4">
          <h3 class="h5 mb-3">Selected Holidays</h3>
          <div class="selected-holidays-list" style="max-height: 250px; overflow-y: auto">
            <div v-if="selectedHolidays.length === 0" class="text-muted">No holidays selected</div>
            <div
              v-for="holiday in selectedHolidays"
              :key="holiday.id"
              class="d-flex justify-content-between align-items-center p-2 mb-2 bg-light rounded"
            >
              <span>{{ formatDate(holiday.date) }}</span>
              <button @click="removeHoliday(holiday)" class="btn btn-sm btn-outline-danger">
                Remove
              </button>
            </div>
          </div>
        </div>

        <div class="mt-4 text-right">
          <button
            @click="saveHolidays"
            class="btn btn-primary"
            :disabled="selectedHolidays.length === 0"
          >
            Save Holidays
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style>
.gazette-calendar {
  margin: 0 auto;
}

/* Custom styling for holiday events */
.holiday-event {
  background-color: #007bff !important;
  border: none !important;
  color: white !important;
  padding: 2px 4px !important;
  border-radius: 3px !important;
}

.holiday-title {
  font-size: 0.8em;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* Bootstrap 4 specific calendar customizations */
.fc .fc-toolbar-title {
  font-size: 1.5rem !important;
}

.fc .fc-button {
  padding: 0.375rem 0.75rem;
}

.fc .fc-button-primary {
  background-color: #007bff;
  border-color: #007bff;
}

.fc .fc-button-primary:hover {
  background-color: #0056b3;
  border-color: #0056b3;
}

.fc .fc-button-primary:disabled {
  background-color: #80bdff;
  border-color: #80bdff;
}

.fc-day:hover {
  background-color: rgba(0, 123, 255, 0.05);
  cursor: pointer;
}

/* Bootstrap card customizations */
.card {
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}
</style>
