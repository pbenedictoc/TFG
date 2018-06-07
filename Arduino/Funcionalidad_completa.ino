//Variables globales-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
float master;
float cache;
int porCien;
float t;
String resultado;
int datos;
float humedad;
float temperatura;
unsigned long tiempoSeguridad=5000;

//Captamos las valvulas disponibles a mano, no hay otra forma, de una a cuatro. En este caso solo cojo una.
int numeroValvulas=4;
int valvulas[4];
float valvulaMaster[4];
String valvulaResultado[4];
unsigned long seguridadValvula[4];
//En el array obertura, guardamos los criterios de obertura de las válvulas, en orden y tanto %.
int obertura[]={25, 30, 40, 50};

//Para la demostración
boolean agua;

//Funciones globales-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

//Main inicial-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
void setup() {
  Serial.begin(9600);
  for(int i=2; i<14; i++){
    if(i!=6){
      pinMode(i, OUTPUT);
    }
  }

  for(int i=0; i<numeroValvulas; i++){
    seguridadValvula[i]=0;
  }
  agua=false;
}

//Main loop----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
void loop(){
  /*Aquí llegarían de forma analógica los sensores tanto de humedad como de temperatura del ambiente que recoge un único sensor. Este nos devuelve 40 bits de un número binario (0011 0101 0000 0000 0001 1000 0000 0000 0100 1101)
  
  Gracias a la librería que adjunto en la docuemntación que facilita arduino se pueden recoger los valores y transformarlos a temperatura y humedad. La libreria la proporciona el propio proyecto Arduino y ya están incluidas en este proyecto.

  humedad=dht,readHumidity();
  temperatura=dht.readTemperature();
  
  En este caso, como aún no he recibido los sensores los pondré de manera predeterminada, aún así la funcionalidad de estos estrará implementada tanto en la base de datos como en el Arduino*/
  humedad=30.0;
  temperatura=21.0;
  
  for(int v=0; v<numeroValvulas; v++){
    cache=analogRead(v);
    if(cache!=valvulaMaster[v]){
      valvulaMaster[v]=cache;
      t=(valvulaMaster[v]*100)/1023;
      t-=100;
      t*=(-1);
      porCien=t;
    }
    valvulas[v]=porCien;
  
    if(valvulas[v]<5){
      resultado="Seco";
    }else if(valvulas[v]>=5 && valvulas[v]<20){
      resultado="Poco húmedo";
    }else if(valvulas[v]>=20 && valvulas[v]<60){
      resultado="Húmedo";
    }else if(valvulas[v]>=60 && valvulas[v]<80){
      resultado="Muy húmedo";
    }else if(valvulas[v]>=80 && valvulas[v]<90){
      resultado="Mojado";
    }else{
      resultado="Inundado";
    }
  
    if(valvulaResultado[v]!=resultado && seguridadValvula[v]==0){
     digitalWrite((v+7), HIGH);
     valvulaResultado[v]=resultado;

         if(valvulas[v]<obertura[v]){
           digitalWrite((v+2), HIGH);
           seguridadValvula[v]=millis();
         }else{
           digitalWrite((v+2), LOW);
           seguridadValvula[v]=millis();
         }
    }else if((millis()-seguridadValvula[v])>=tiempoSeguridad){
      digitalWrite((v+7), LOW);
      seguridadValvula[v]=0;
    }
  }

  //Esta parte es inecesario, pero la representación ante el tribunal es muy visual, así que está implementada, pero luego se puede extraer sin problemas
  agua=false;
  for(int i=0; i<numeroValvulas; i++){
    if(valvulas[i]<obertura[i]){
      agua=true;
    }
  }
  
  if(agua==true){
    for(int i=11; i<14; i++){
       digitalWrite(i, HIGH);
    }
  }else{
    for(int i=11; i<14; i++){
       digitalWrite(i, LOW);
    }
  }

  
  datos="";
  if(Serial.available()>0){
    while(Serial.available()>0){
      datos=Serial.read();
    }

    //El 49 significa que el carácter que se pasa es igual a uno, ya que 49 en ascii es 1
    if(datos==49){
      String datosArray="[";
      for(int i=0; i<numeroValvulas; i++){
        if(i!=0){
          datosArray=datosArray+",";
        }
        datosArray=datosArray+"["+String(valvulas[i])+","+obertura[i]+"]";
      }
      datosArray=datosArray+"]";
      Serial.println("-*[");
      Serial.println(datosArray);
      Serial.println(",");
      Serial.println(humedad);
      Serial.println(",");
      Serial.println(temperatura);
      Serial.println("]^-");
    }else{
      Serial.println("-*"); 
      Serial.write("NULL");
      Serial.println("^-");  
    }
  }
}
