import imgCommunity from './images/community.png';
import imgPlayerKylianMbappe from './images/players/kylian-mbappe.png';
import imgPlayerLionelMessi from './images/players/lionel-messi.png';
import imgPlayerLuisCampos from './images/players/luis-campos.png';
import imgPlayerAlKhelaifi from './images/players/al-khelaifi.png';

export const community: IAppPreviewCommunity = {
  image: imgCommunity,
  name: 'PSG',
  membersCount: 39,
};

export const messages: Array<IAppPreviewMessageGroup> = [
  {
    person: {
      avatar: imgPlayerLionelMessi,
      name: 'Lionel Messi',
    },
    messages: [
      {
        text: '✈️ 🇺🇸',
        time: '16:43',
      },
    ],
  },
  {
    person: {
      avatar: imgPlayerKylianMbappe,
      name: 'Kylian Mbappé',
    },
    messages: [
      {
        text: '⛔ I would not trigger the option of an extra year (until 2025) in my contract.',
        time: '20:01',
      },
    ],
  },
  {
    person: {
      avatar: imgPlayerLuisCampos,
      name: 'Luís Campos',
    },
    messages: [
      {
        text: 'PSG and Kylian Mbappé were in talks about an extension beyond 2025, but this letter by the Frenchman is perceived as a very negative signal and almost like a ‘casus belli’.',
        time: '20:36',
      },
    ],
  },
  {
    person: {
      avatar: imgPlayerAlKhelaifi,
      name: 'Nasser Al-Khelaifi',
    },
    messages: [
      {
        text: '❗PSG are tired of this situation. Either Mbappé gives positive signs for an extension, or he will be sold in the coming weeks. Under no circumstances will Kylian Mbappé leave free.',
        time: '21:00',
      },
    ],
  },
];
