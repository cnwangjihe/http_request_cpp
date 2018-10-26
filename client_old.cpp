#include <winsock2.h>
#include <windows.h>
#include <wininet.h>
#include <iostream>
#include <tchar.h>
#include <string>
#include <Urlmon.h>
#include <locale>

using namespace std;

#define ERROR_OPEN_FILE       10
#define ERROR_MEMORY          11
#define ERROR_SIZE            12
#define ERROR_INTERNET_OPEN   13
#define ERROR_INTERNET_CONN   14
#define ERROR_INTERNET_REQ    15
#define ERROR_INTERNET_SEND   16

const string IP = "10.3.37.198";
const char url[]   = "/api.php";
const char *type       = "text/plain";
const char boundary[]  = "--BOUNDARY---";
const char nameForm[]  = "file";     //Input form name

using namespace std;

string GetFileName(string File)
{
	string FileName=File;
	int pos;
	pos = FileName.rfind('\\');
	FileName.erase(0,pos+1);
	return FileName;
}

int MakePost(string File)
{
	string FileName=GetFileName(File);
	const char *filename   = FileName.c_str();   //Filename to be loaded
	const char *filepath   = File.c_str();   //Filename to be loaded
	const char *iaddr = IP.c_str();      

	char hdrs[512]={'-'};                  //Headers
	char * buffer;                   //Buffer containing file + headers
	char * content;                  //Buffer containing file
	FILE * pFile;                    //File pointer
	long lSize;                      //File size
	size_t result;                   
		// Open file
	 pFile = fopen ( filepath , "rb" );
	 if (pFile==NULL) 
	 {
		cerr << "ERROR_OPEN_FILE";
		getchar();
		return ERROR_OPEN_FILE;
	}
//	printf("OPEN_FILE\n");
	
	 // obtain file size:
	fseek (pFile , 0 , SEEK_END);
	lSize = ftell (pFile);
	rewind (pFile);
	
	 // allocate memory to contain the whole file:
	content = (char*) malloc (sizeof(char)*(lSize+1));
	if (content == NULL) 
	{
		cerr << "ERROR_MEMORY\n";
		getchar();
		return ERROR_OPEN_FILE;
	}
//	printf("MEMORY_ALLOCATED\t \"%ld\" \n",&lSize);
	 // copy the file into the buffer:
	result = fread (content,1,lSize,pFile);
	if ((long int)result != lSize) 
	{
		cerr << "ERROR_SIZE\n";
		getchar();
		return ERROR_OPEN_FILE;
	}
//	printf("SIZE_OK\n");
	
	content[lSize] = '\0';
	
	 // terminate
	fclose (pFile);
//	printf("FILE_CLOSE\n");
	 //allocate memory to contain the whole file + HEADER
	buffer = (char*) malloc (sizeof(char)*lSize + 2048);
	
	 //print header
	sprintf(buffer,"--%s\r\nContent-Disposition: form-data; name=\"%s\"; filename=\"%s\"\r\n",boundary,nameForm,filename);
	sprintf(buffer,"%sContent-Type: %s\r\n",buffer,type);
	sprintf(buffer,"%s\r\n%s",buffer,content);
	sprintf(buffer,"%s\r\n--%s--\r\n",buffer,boundary);
	sprintf(hdrs,"Content-Type: multipart/form-data; boundary=%s",boundary);
	sprintf(hdrs,"%s\r\nContent-Length: %d",hdrs,strlen(buffer));
	WSADATA wsaData;
	SOCKET Socket;
	SOCKADDR_IN SockAddr;
	struct hostent *host;
	locale local;
	string website_HTML;

	//HTTP GET
	string get_http = string() + "POST " + url + " HTTP/1.1\r\nHost: " + IP + "\r\nConnection: close\r\n" + hdrs + "\r\n\r\n" + buffer;
	if (WSAStartup(MAKEWORD(2,2), &wsaData) != 0)
	{
		cout << "WSAStartup failed.\n";
		system("pause");
		//return 1;
	}

	Socket=socket(AF_INET,SOCK_STREAM,IPPROTO_TCP);
	struct in_addr addr = { 0, 0, 0, 0 };
	addr.s_addr = inet_addr(IP.c_str());
	host =  gethostbyaddr((char *) &addr, 4, AF_INET);

	SockAddr.sin_port=htons(80);
	SockAddr.sin_family=AF_INET;
	SockAddr.sin_addr.s_addr = *((unsigned long*)host->h_addr);

	if(connect(Socket,(SOCKADDR*)(&SockAddr),sizeof(SockAddr)) != 0)
	{
		cerr << "Could not connect";
		return 1;
	}

	// send POST / HTTP

	puts(get_http.c_str());
	system("pause");
	send(Socket,get_http.c_str(), strlen(get_http.c_str()),0 );

	cerr << "Post Done\n";
	closesocket(Socket);
	WSACleanup();
	return 0;
}

void GetFile(string FileName)
{
	string url="http://"+IP+"/upload/"+FileName;
	HRESULT res=URLDownloadToFile(NULL,url.c_str(),FileName.c_str(),0,NULL);
	if (res == S_OK)
		cerr << "Download Done\n";
	if (res == E_OUTOFMEMORY)
		cerr << "Buffer length invalid, or insufficient memory\n";
	if (res == INET_E_DOWNLOAD_FAILURE)
		cerr << "URL is invalid\n";
	return ;
}

int main()
{
	string input;
	MakePost("yuw.txt");
	return 0;
}