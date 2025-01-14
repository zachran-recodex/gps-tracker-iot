// Most recent codes

#include <TinyGPS++.h>
#include <HardwareSerial.h>
#include <ezButton.h>

TinyGPSPlus gps;
HardwareSerial mySerial(2);    // GPS Module pada Serial2
HardwareSerial mySerial2(1);   // GSM Module pada Serial1 (RX 18 TX 19)

unsigned long interv = 20000;   // Interval pengiriman 20 detik
unsigned long prevTimer = 0;

bool emergencyState = false;
int buzzer = 21;

ezButton button(22);

double lat;
double lng;



// Koordinat testing
//double latt = -6.973037111090844;
//double longg = 107.63145534705285;

// URL testing
const String api_url = "iot.recodex.id"; // Domain nanti diubah
const String api_path = "/api/gps";

void setup() {
    Serial.begin(115200);
    mySerial.begin(9600, SERIAL_8N1, 16, 17);  // GPS Module
    mySerial2.begin(115200, SERIAL_8N1, 18, 19); // GSM Module
    pinMode(buzzer, OUTPUT); // output pin buzzer
    delay(3000);


    Serial.println("Initializing...");

    mySerial2.println("AT&F");
    updateSerial();

    // Test AT Command
    mySerial2.println("AT");
    updateSerial();

    // Nonaktifkan HTTPS
    mySerial2.println("AT+HTTPSSL=0"); // Nonaktifkan SSL
    updateSerial();

    // Setup GPRS
    setupGPRS();

    Serial.println("In da loop"); // kode debugging, ignore
}

void loop() {
    button.loop();
    // Kode loop tetap sama
    unsigned long timer = millis();

    while (mySerial.available() > 0) {
        char c = mySerial.read();   // Read data from GPS
        Serial.print(c);
        gps.encode(c);             // Feed it to TinyGPS++

        // If a valid location is available, print it
        if (gps.location.isUpdated()) {
           lat = gps.location.lat();
           lng = gps.location.lng();
            Serial.print("Latitude: ");
            Serial.println(lat, 6); // gps.location.lat() return latitude tipe data (double);
            Serial.print("Longitude: ");
            Serial.println(lng, 6); // sama seperti lat, cuma return longtitude (double);

                        // Send to API

        }
    }

    if(timer - prevTimer >= interv) {
        prevTimer = timer;

        /*
        latt = latt + 0.0001;
        longg = longg + 0.0001;
        */
        sendToAPI(lat, lng);
    }


    // block kode ini buat emergency button!

    if(button.isPressed()){
      emergencyState = !emergencyState;
    }
    if(emergencyState){
      tone(buzzer, 700);
      Serial.println("in da buzz"); // kode debugging, ignore.
      } else {
        noTone(buzzer);
      }

}

void setupGPRS() {
    // Reset HTTP
    mySerial2.println("AT+HTTPTERM");
    updateSerial();
    delay(1000);

    // Setup GPRS - Sesuaikan APN dengan provider
    mySerial2.println("AT+SAPBR=3,1,\"CONTYPE\",\"GPRS\"");
    updateSerial();
    mySerial2.println("AT+SAPBR=3,1,\"APN\",\"telkomsel\""); // Ganti "internet" dengan APN provider Anda
    updateSerial();
    mySerial2.println("AT+SAPBR=1,1");
    updateSerial();
    delay(2000);

    // Init HTTP tanpa SSL
    mySerial2.println("AT+HTTPINIT");
    updateSerial();
    mySerial2.println("AT+HTTPSSL=0"); // Pastikan SSL dinonaktifkan
    updateSerial();
    mySerial2.println("AT+HTTPPARA=\"CID\",1");
    updateSerial();
}

void sendToAPI(float latitude, float longitude) {
    String jsonData = "{\"latitude\":" + String(latitude, 6) +
                     ",\"longitude\":" + String(longitude, 6) +
                     ",\"emergency\":" + String(emergencyState ? "true" : "false") + "}";
    Serial.println("Sending data: " + jsonData);

    // cek apakah terkoneksi ke GPRS, kalau iya dia return IP kita
    mySerial2.println("AT+SAPBR=2,1");
    updateSerial();

    // Check Signal Quality, kalo bagus valuenya diatas 10
    mySerial2.println("AT+CSQ");
    updateSerial();


    // Set URL dengan http://
    String url = "http://" + api_url + api_path; // Ganti https:// dengan http://
    mySerial2.println("AT+HTTPPARA=\"URL\",\"" + url + "\"");
    updateSerial();
    delay(500);

    // Content type JSON
    mySerial2.println("AT+HTTPPARA=\"CONTENT\",\"application/json\"");
    updateSerial();
    delay(500); // semua yang 500 tadinya 1000

    // Send data
    mySerial2.println("AT+HTTPDATA=" + String(jsonData.length()) + ",10000");
    updateSerial();
    delay(500);

    mySerial2.println(jsonData);
    updateSerial();
    delay(3000); // ini tadinya 5000 jadi 3000

    // POST request
    mySerial2.println("AT+HTTPACTION=1");
    updateSerial();
    delay(3000); // Tunggu respon dari server / ini tadinya 10000 jadi 3000 biar cepet aja, kalo error ganti lagi nanti.

    // Read response
    mySerial2.println("AT+HTTPREAD");
    updateSerial();
    delay(1000);

    Serial.println("Data sent successfully");

}

void updateSerial() {
    delay(500);
    while (Serial.available()) {
        mySerial2.write(Serial.read());
    }
    while (mySerial2.available()) {
        Serial.write(mySerial2.read());
    }
}
