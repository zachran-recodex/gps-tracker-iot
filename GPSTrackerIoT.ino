#include <TinyGPS++.h>
#include <HardwareSerial.h>

TinyGPSPlus gps;
HardwareSerial mySerial(2);    // GPS Module pada Serial2
HardwareSerial mySerial2(1);   // GSM Module pada Serial1 (RX 18 TX 19)

unsigned long interv = 20000;   // Interval pengiriman 20 detik
unsigned long prevTimer = 0;

// Koordinat testing
double latt = -6.973037111090844;
double longg = 107.63145534705285;

// URL Production
const String api_url = "iot.recodex.id"; // Domain sudah benar
const String api_path = "/api/gps";

void setup() {
    Serial.begin(115200);
    mySerial.begin(9600, SERIAL_8N1, 16, 17);  // GPS Module
    mySerial2.begin(115200, SERIAL_8N1, 18, 19); // GSM Module
    delay(3000);

    Serial.println("Initializing...");

    // Test AT Command
    mySerial2.println("AT");
    updateSerial();

    // Nonaktifkan HTTPS
    mySerial2.println("AT+HTTPSSL=0"); // Nonaktifkan SSL
    updateSerial();

    // Setup GPRS
    setupGPRS();
}

void loop() {
    // Kode loop tetap sama
    unsigned long timer = millis();
    if(timer - prevTimer >= interv) {
        prevTimer = timer;
        latt = latt + 0.0001;
        longg = longg + 0.0001;
        sendToAPI(latt, longg);
    }

    updateSerial();
}

void setupGPRS() {
    // Reset HTTP
    mySerial2.println("AT+HTTPTERM");
    updateSerial();
    delay(1000);

    // Setup GPRS - Sesuaikan APN dengan provider
    mySerial2.println("AT+SAPBR=3,1,\"CONTYPE\",\"GPRS\"");
    updateSerial();
    mySerial2.println("AT+SAPBR=3,1,\"APN\",\"internet\""); // Ganti "internet" dengan APN provider Anda
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
    String jsonData = "{\"latitude\":" + String(latitude, 6) + ",\"longitude\":" + String(longitude, 6) + "}";
    Serial.println("Sending data: " + jsonData);

    // Set URL dengan http://
    String url = "http://" + api_url + api_path; // Ganti https:// dengan http://
    mySerial2.println("AT+HTTPPARA=\"URL\",\"" + url + "\"");
    updateSerial();
    delay(1000);

    // Content type JSON
    mySerial2.println("AT+HTTPPARA=\"CONTENT\",\"application/json\"");
    updateSerial();
    delay(1000);

    // Send data
    mySerial2.println("AT+HTTPDATA=" + String(jsonData.length()) + ",10000");
    updateSerial();
    delay(1000);

    mySerial2.println(jsonData);
    updateSerial();
    delay(5000);

    // POST request
    mySerial2.println("AT+HTTPACTION=1");
    updateSerial();
    delay(10000); // Tunggu respon dari server

    // Read response
    mySerial2.println("AT+HTTPREAD");
    updateSerial();
    delay(1000);

    Serial.println("Data sent successfully");
    delay(1000);
}

void updateSerial() {
    delay(1000);
    while (Serial.available()) {
        mySerial2.write(Serial.read());
    }
    while (mySerial2.available()) {
        Serial.write(mySerial2.read());
    }
}
