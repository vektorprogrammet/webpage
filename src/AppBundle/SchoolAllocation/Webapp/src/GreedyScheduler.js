const weekDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

export function scheduleGreedily(schedule) {
  schedule = schedule.deepCopy();
  const assistants = schedule.queuedAssistants;
  let j = 0;
  while (j < assistants.length) {
    const assistant = assistants[j];
    const double = assistant.double;
    if (double) {
      for (let i = 0; i < weekDays.length; i++) {
        const day = weekDays[i];
        if (assistant[day] && schedule.hasCapacity(day, 1) && schedule.hasCapacity(day, 2)) {
          schedule.assignAssistantTo(assistant, day, 1);
          schedule.assignAssistantTo(assistant, day, 2);
          break;
        }
      }
    } else if (assistant.preferredGroup === 1) {
      for (let i = 0; i < weekDays.length; i++) {
        const day = weekDays[i];
        if (assistant[day] && schedule.hasCapacity(day, 1)) {
          schedule.assignAssistantTo(assistant, day, 1);
          break;
        }
      }
    } else if (assistant.preferredGroup === 2) {
      for (let i = 0; i < weekDays.length; i++) {
        const day = weekDays[i];
        if (assistant[day] && schedule.hasCapacity(day, 2)) {
          schedule.assignAssistantTo(assistant, day, 2);
          break;
        }
      }
    } else {
      for (let i = 0; i < weekDays.length; i++) {
        const day = weekDays[i];
        if (assistant[day] && schedule.hasCapacity(day, 1)) {
          schedule.assignAssistantTo(assistant, day, 1);
          break;
        } else if (assistant[day] && schedule.hasCapacity(day, 2)) {
          schedule.assignAssistantTo(assistant, day, 2);
          break;
        }
      }
    }
    j++;
  }

  return schedule;
}
