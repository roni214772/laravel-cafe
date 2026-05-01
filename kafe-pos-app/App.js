import { useRef, useState } from 'react';
import { StatusBar } from 'expo-status-bar';
import { StyleSheet, View, BackHandler, ActivityIndicator, Platform, Alert } from 'react-native';
import { WebView } from 'react-native-webview';
import { useEffect } from 'react';
import Constants from 'expo-constants';
import * as Notifications from 'expo-notifications';

const SITE_URL = 'https://caffe-pos.com/adisyon';

// Notification handler ayarla
Notifications.setNotificationHandler({
  handleNotification: async () => ({
    shouldShowAlert: true,
    shouldPlaySound: true,
    shouldSetBadge: true,
  }),
});

export default function App() {
  const webViewRef = useRef(null);
  const [canGoBack, setCanGoBack] = useState(false);
  const [loading, setLoading] = useState(true);
  const lastUrlRef = useRef('');
  const notificationListener = useRef();
  const responseListener = useRef();

  useEffect(() => {
    // Push notification setup
    registerForPushNotifications();

    // Notification listener (bildirim geldi)
    notificationListener.current = Notifications.addNotificationReceivedListener(notification => {
      console.log('📬 Bildirim alındı:', notification);
    });

    // Bildirim tıklandığında
    responseListener.current = Notifications.addNotificationResponseReceivedListener(response => {
      console.log('👆 Bildirim tıklandı');
    });

    if (Platform.OS === 'android') {
      const backHandler = BackHandler.addEventListener('hardwareBackPress', () => {
        if (canGoBack && webViewRef.current) {
          webViewRef.current.goBack();
          return true;
        }
        return false;
      });
      return () => {
        backHandler.remove();
        if (notificationListener.current) {
          Notifications.removeNotificationSubscription(notificationListener.current);
        }
        if (responseListener.current) {
          Notifications.removeNotificationSubscription(responseListener.current);
        }
      };
    }

    return () => {
      if (notificationListener.current) {
        Notifications.removeNotificationSubscription(notificationListener.current);
      }
      if (responseListener.current) {
        Notifications.removeNotificationSubscription(responseListener.current);
      }
    };
  }, [canGoBack]);

  return (
    <View style={styles.container}>
      <StatusBar style="light" backgroundColor="#0a0a0a" />
      <View style={styles.statusBarSpacer} />
      <WebView
        ref={webViewRef}
        source={{ uri: SITE_URL }}
        style={styles.webview}
        onNavigationStateChange={(navState) => {
          setCanGoBack(navState.canGoBack);
          if (!navState.loading && navState.url) {
            lastUrlRef.current = navState.url;
          }
        }}
        onLoadEnd={() => setLoading(false)}
        injectedJavaScript={`
          (function() {
            var error = document.querySelector('.error');
            var path = window.location.pathname;
            window.ReactNativeWebView.postMessage(JSON.stringify({
              type: 'pageInfo',
              path: path,
              error: error ? error.innerText.trim() : null
            }));
          })();
          true;
        `}
        onMessage={(event) => {
          try {
            const data = JSON.parse(event.nativeEvent.data);
            if (data.type === 'pageInfo') {
              if (data.error) {
                Alert.alert('Giriş Hatası', data.error);
              } else if (data.path === '/subscription/select') {
                Alert.alert('Abonelik Gerekli', 'Hesabınızın aboneliği aktif değil. Lütfen abonelik satın alın.');
              } else if (data.path === '/subscription/pending') {
                Alert.alert('Abonelik Bekleniyor', 'Abonelik talebiniz inceleniyor, lütfen bekleyin.');
              }
            }
          } catch (e) {}
        }}
        javaScriptEnabled={true}
        domStorageEnabled={true}
        thirdPartyCookiesEnabled={true}
        sharedCookiesEnabled={true}
        userAgent="Mozilla/5.0 (Linux; Android 10; Mobile) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Mobile Safari/537.36"
        startInLoadingState={true}
        allowsBackForwardNavigationGestures={true}
        pullToRefreshEnabled={true}
        setSupportMultipleWindows={false}
        onShouldStartLoadWithRequest={(request) => {
          return true;
        }}
        renderLoading={() => (
          <View style={styles.loadingContainer}>
            <ActivityIndicator size="large" color="#27A0B1" />
          </View>
        )}
      />
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#0a0a0a',
  },
  statusBarSpacer: {
    height: Constants.statusBarHeight,
    backgroundColor: '#0a0a0a',
  },
  webview: {
    flex: 1,
  },
  loadingContainer: {
    position: 'absolute',
    top: 0,
    left: 0,
    right: 0,
    bottom: 0,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#0a0a0a',
  },
});

// Push notification registration fonksiyonu
async function registerForPushNotifications() {
  try {
    // Mevcut permission durumu kontrol et
    const { status: existingStatus } = await Notifications.getPermissionsAsync();
    let finalStatus = existingStatus;

    // Eğer izin alınmamışsa iste
    if (existingStatus !== 'granted') {
      const { status } = await Notifications.requestPermissionsAsync();
      finalStatus = status;
    }

    // Hala izin alınamadıysa return et
    if (finalStatus !== 'granted') {
      console.log('❌ Bildirim izni reddedildi');
      return;
    }

    // Expo push token al
    const token = (await Notifications.getExpoPushTokenAsync()).data;
    console.log('✅ Expo Push Token:', token);

    // Token'ı backend'e gönder
    await sendTokenToBackend(token);
  } catch (error) {
    console.error('❌ Push notification setup hatası:', error);
  }
}

// Token'ı backend'e gönder
async function sendTokenToBackend(token) {
  try {
    const response = await fetch('https://caffe-pos.com/api/save-notification-token', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        expo_push_token: token,
        platform: Platform.OS,
      }),
    });

    if (response.ok) {
      console.log('✅ Token backend\'e gönderildi');
    } else {
      console.error('❌ Token gönderme başarısız:', response.status);
    }
  } catch (error) {
    console.error('❌ Token gönderme hatası:', error);
  }
}
