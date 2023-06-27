import moment from 'moment';

// function formatDateFull(date: Date) {
//   return date.toLocaleDateString([], {
//     dateStyle: 'full',
//   });
// }

function formatDate(date: Date) {
  return date.toLocaleDateString([], {
    dateStyle: 'long',
  });
}

function formatTime(date: Date) {
  return date.toLocaleTimeString(['en-GB'], {
    hour: '2-digit',
    minute: '2-digit',
  });
}

export function getDateAndTime(date: Date) {
  const today = new Date();
  const yesterday = new Date();
  yesterday.setDate(yesterday.getDate() - 1);

  const strToday = formatDate(today);
  const strYesterday = formatDate(yesterday);
  const strDate = formatDate(date);
  const strTime = formatTime(date);

  if (strToday === strDate) {
    return 'Today ' + strTime;
  }

  if (strYesterday === strDate) {
    return 'Yesterday ' + strTime;
  }

  if (moment(date).format('YYYY') === moment(today).format('YYYY')) {
    return (
      moment(date).format('MMMM D') + ' at ' + moment(date).format('HH:mm')
    );
  }

  return (
    moment(date).format('MMMM D, YYYY') + ' at ' + moment(date).format('HH:mm')
  );
}
