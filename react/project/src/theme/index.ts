import { extendTheme } from '@chakra-ui/react';
import globalStyle from './global';

import TextComponent from './components/Text';
import LinkComponent from './components/Link';
import HeadingComponent from './components/Heading';
import ImageComponent from './components/Image';

const theme = extendTheme({
  components: {
    Text: TextComponent,
    Link: LinkComponent,
    Heading: HeadingComponent,
    Image: ImageComponent,
  },
  styles: {
    global: globalStyle,
  },
});

export default theme;
