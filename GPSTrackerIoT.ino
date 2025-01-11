#include <TinyGPS++.h>
#include <HardwareSerial.h>

TinyGPSPlus gps;               // Create TinyGPS++ object
HardwareSerial mySerial(2);
HardwareSerial mySerial2(1);    // RX 18 TX 19

void setup() {
    Serial.begin(115200);      // For debugging (via USB)
    mySerial.begin(9600, SERIAL_8N1, 16, 17);
    mySerial2.begin(115200, SERIAL_8N1, 18, 19);  // GPS at 9600 baud, RX=GPIO25, TX=GPIO26
    delay(1000);
    Serial.println("Beginning");

  mySerial.println("AT"); 
  updateSerial();
  
  mySerial.println("AT+CMGF=1"); 
  updateSerial();
  mySerial.println("AT+CNMI=1,2,0,0,0");
  updateSerial();

  sendMSG("This is the end...");
}

void loop() {
    while (mySerial.available() > 0) {
        char c = mySerial.read();   // Read data from GPS
        Serial.print(c); 
        gps.encode(c);             // Feed it to TinyGPS++

        // If a valid location is available, print it
        if (gps.location.isUpdated()) {
            Serial.print("Latitude: ");
            Serial.println(gps.location.lat(), 6); // gps.location.lat() return latitude tipe data (double);
            Serial.print("Longitude: ");
            Serial.println(gps.location.lng(), 6); // sama seperti lat, cuma return longtitude (double);
        }
    }

    updateSerial();

}

void updateSerial()
{
  delay(500);
  while (Serial.available()) 
  {
    mySerial2.write(Serial.read());
  }
  while(mySerial2.available()) 
  {
    Serial.write(mySerial2.read());
  }
}

void sendMSG(String x){
  mySerial2.println("AT+CMGF=1"); // Configuring TEXT mode
  updateSerial();
  mySerial2.println("AT+CMGS=\"+628112405775\"");//change ZZ with country code and xxxxxxxxxxx with phone number to sms
  updateSerial();
  mySerial2.print(x); //text content
  updateSerial();
  Serial.println();
  Serial.println("Message Sent");
  mySerial2.write(26);
}

