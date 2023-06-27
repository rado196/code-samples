import imgCommunity from './images/community.png';
import imgPlayerCristianoRonaldo from './images/players/cristiano-ronaldo.png';
import imgPlayerGaryNeville from './images/players/gary-neville.png';
import imgPlayerWayneRooney from './images/players/wayne-rooney.png';

export const community: IAppPreviewCommunity = {
  image: imgCommunity,
  name: 'Manchester United',
  membersCount: 32,
};

export const messages: Array<IAppPreviewMessageGroup> = [
  {
    person: {
      avatar: imgPlayerCristianoRonaldo,
      name: 'Cristiano Ronaldo',
    },
    messages: [
      {
        text: "Manchester right now to compare with that club, I think it's behind in my opinion, which is something that surprised me. A club with this dimension should be the top of the tree in my opinion and they are not, unfortunately. They are not in that level. But I hope the next years they can reach to be in a top level.",
        time: '16:43',
      },
    ],
  },
  {
    person: {
      avatar: imgPlayerGaryNeville,
      name: 'Gary Neville',
    },
    messages: [
      {
        text: 'When you look at whether Cristiano should be selected, Manchester United are better without him - and Erik ten Hag knows that. The only thing that Cristiano and the club can do is get together in the next week and end the relationship. Cristiano is too good of a player, too fantastic of a character and the club have got to move on.',
        time: '16:48',
      },
    ],
  },
  {
    person: {
      avatar: imgPlayerWayneRooney,
      name: 'Wayne Rooney',
    },
    messages: [
      {
        text: "For Cristiano, just get your head down and work and be ready to play when the manager needs you. If he does that, he will be an asset. If he doesn't, it will become an unwanted distraction.",
        time: '16:55',
      },
    ],
  },
];
