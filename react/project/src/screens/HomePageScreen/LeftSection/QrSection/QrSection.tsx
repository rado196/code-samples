import React, { useEffect, useState } from 'react';
import { IProps } from './types';
import { Box, Center, Spinner } from '@chakra-ui/react';
import { QRCode } from 'react-qrcode-logo';
import imgLogo from '~assets/images/logo/logo-white.png';

const QR_SIZE_SECTION = 140;
const QR_SIZE_LOGO = QR_SIZE_SECTION / 3.2;
const QR_EYE_RADIUS = QR_SIZE_SECTION / 10;
const QR_BG_COLOR = 'white';
const QR_FG_COLOR = 'black';

function QrSection(props: PropsType<IProps>) {
  const [loading, setLoading] = useState<boolean>(true);
  useEffect(() => {
    setTimeout(() => {
      setLoading(false);
    }, 600);
  }, []);

  return (
    <Box
      background="white"
      borderColor="#dadada"
      borderStyle="solid"
      borderWidth="1px"
      borderRadius="10px"
      padding="5px"
      height={`${QR_SIZE_SECTION + 30}px`}
      width={`${QR_SIZE_SECTION + 30}px`}
    >
      {loading ? (
        <Center
          height={`${QR_SIZE_SECTION + 30}px`}
          width={`${QR_SIZE_SECTION + 30}px`}
        >
          <Spinner size="sm" />
        </Center>
      ) : (
        <QRCode
          value={process.env.NEXT_PUBLIC_QR_REDIRECT_URL}
          size={QR_SIZE_SECTION}
          bgColor={QR_BG_COLOR}
          fgColor={QR_FG_COLOR}
          logoImage={imgLogo.src}
          logoWidth={QR_SIZE_LOGO}
          logoHeight={QR_SIZE_LOGO}
          qrStyle="dots"
          quietZone={10}
          eyeColor={QR_FG_COLOR}
          eyeRadius={[
            [QR_EYE_RADIUS, QR_EYE_RADIUS, 0, QR_EYE_RADIUS],
            [QR_EYE_RADIUS, QR_EYE_RADIUS, QR_EYE_RADIUS, 0],
            [QR_EYE_RADIUS, 0, QR_EYE_RADIUS, QR_EYE_RADIUS],
          ]}
        />
      )}
    </Box>
  );
}

export default QrSection;
