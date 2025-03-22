export default class GoogleCalendarService {
  static async fetchEvents() {
    const response = await fetch('/api/calendar/events');
    return response.json();
  }

  static async createEvent(eventData) {
    const response = await fetch('/api/calendar/events', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(eventData),
    });
    return response.json();
  }
}
