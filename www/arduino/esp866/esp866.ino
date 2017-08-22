#include <ESP8266WiFi.h>

const char* ssid = "Law\'s iPhone";
const char* password = "lawlawlaw";

// Create an instance of the server
// specify the port to listen on as an argument
WiFiServer server(80);

char serverName[] = "www.cebusolution.com";
char createData[] = "/api/data/create?token=B9AB3723BAE48&level=2&warning=2&current=2";
char token[] = "token=B9AB3723BAE48&level=2&warning=2&current=2";
int inChar;
char outBuf[64];

void setup() {

  Serial.begin(115200);
  delay(10);
  
  // prepare GPIO2
  pinMode(13, OUTPUT);
  digitalWrite(13, 0);
  
  // Connect to WiFi network
  Serial.println();
  Serial.println();
  Serial.print("Connecting to ");
  Serial.println(ssid);
  
  WiFi.begin(ssid, password);
  
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println("");
  Serial.println("WiFi connected");

  // Start the server
  
  server.begin();
  Serial.println("Server started");
  
  // Print the IP address
  Serial.println(WiFi.localIP());
}

void loop() {

  // Use WiFiClient class to create TCP connections

  WiFiClient client;
  const int httpPort = 80;
  if (!client.connect(serverName, httpPort)) {
    Serial.println("connection failed");
    return;
  }
  
  // send the header
  sprintf(outBuf,"POST %s HTTP/1.1",createData);
  client.println(outBuf);
  sprintf(outBuf,"Host: %s",serverName);
  client.println(outBuf);
  client.println(F("Connection: close\r\nContent-Type: application/x-www-form-urlencoded"));
  sprintf(outBuf,"Content-Length: %u\r\n",strlen(token));
  client.println(outBuf);
  
  // send the body (variables)
  client.print(token);

  Serial.println(outBuf);
  Serial.println(token);

  // Read all the lines of the reply from server and print them to Serial
  while (client.available()) {
  String line = client.readStringUntil('\r');
    Serial.print(line);
  }
  
  delay(1);
  Serial.println("Client disonnected");
  delay(5000);
}
