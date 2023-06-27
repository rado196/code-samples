import imgBackground from '~assets/images/background/bg-2.png';

const textSelectionStyle = {
  color: 'rgb(3a, 3a, 3a)',
  background: 'rgba(42, 126, 25, 0.25)',
};

const globalStyle = {
  'html': {
    // background: '#e7e7e8',
  },

  'body': {
    'position': 'relative',
    'background': `url(${imgBackground.src})`,
    'backgroundRepeat': 'no-repeat',
    'backgroundSize': 'cover',
    'backgroundPosition': 'center',
    'backgroundAttachment': 'fixed',

    '.body-cover-content': {
      position: 'relative',
      zIndex: 1,
      top: 0,
      bottom: 0,
      left: 0,
      right: 0,
      overflowX: 'hidden',
    },

    '.body-cover-window': {
      backgroundColor: 'rgba(255,255,255,.9)',
      position: 'fixed',
      zIndex: 1,
      top: 0,
      bottom: 0,
      left: 0,
      right: 0,
    },
  },

  '::-moz-selection': textSelectionStyle,
  '::selection': textSelectionStyle,
};

export default globalStyle;
