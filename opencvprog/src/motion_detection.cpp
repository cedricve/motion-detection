#include <opencv2/opencv.hpp>
#include <iostream>
#include <time.h>
#include <dirent.h>
#include <sstream>
#include <dirent.h>
#include <sys/types.h>
#include <sys/stat.h>

using namespace std;
using namespace cv;

bool directoryExists( const char* pzPath )
{
    if ( pzPath == NULL) return false;
    DIR *pDir;
    bool bExists = false;
    pDir = opendir (pzPath);
    if (pDir != NULL)
    {
        bExists = true;
        (void) closedir (pDir);
    }
    return bExists;
}

bool saveImg(Mat image, const string DIRECTORY, const string EXTENSION, const char * DIR_FORMAT, const char * FILE_FORMAT){
    
    stringstream ss;
    time_t seconds;
    struct tm * timeinfo;
    char TIME[80];
    
    time (&seconds);
    timeinfo = localtime (&seconds);
    
    // convert dir...
    strftime (TIME,80,DIR_FORMAT,timeinfo);
    ss.str("");
    ss << DIRECTORY << TIME;
    
    if(!directoryExists(ss.str().c_str()))
        mkdir(ss.str().c_str(), 0777);
    
    // convert image name
    strftime (TIME,80,FILE_FORMAT,timeinfo);
    ss.str("");
    ss << DIRECTORY << TIME << EXTENSION;
    
    // save image
    return imwrite(ss.str().c_str(), image);
}

int main (int argc, char * const argv[]){
    
    // const
    const string DIR = "/home/pi/opencv/pics/";
    const string DIR2 = "/var/www/";
    const string EXT = ".jpg";
    const int DELAY = 300; // mseconds
    
    string DIR_FORMAT = "%d%h%Y";
    string FILE_FORMAT = DIR_FORMAT + "/" + "%d%h%Y_%H%M%S";
    string TIME_FORMAT = "%H";    
	
    // create all necessary instances
    CvCapture * camera = cvCaptureFromCAM(CV_CAP_ANY);
    cvSetCaptureProperty( camera, CV_CAP_PROP_FRAME_WIDTH, 1280 );
    cvSetCaptureProperty( camera, CV_CAP_PROP_FRAME_HEIGHT, 720 );
    
    Mat original =  cvQueryFrame(camera);
    saveImg(original,DIR,EXT,DIR_FORMAT.c_str(),FILE_FORMAT.c_str());
            
    Mat next_frame = original;
    Mat current_frame = cvQueryFrame(camera);
    Mat prev_frame = cvQueryFrame(camera);
    Mat original2;
	
    cvtColor(current_frame, current_frame, CV_RGB2GRAY);
    cvtColor(prev_frame, prev_frame, CV_RGB2GRAY);
    cvtColor(next_frame, next_frame, CV_RGB2GRAY);
	
    Mat d1, d2, result;
    int window = 200;

    // Color for drawing
    Scalar sc(0,255,255);
    Scalar sc2(0,0,255);
    
    while (true){
    
        // Calc differences between the images
        absdiff(next_frame, current_frame, d1);
        absdiff(current_frame, prev_frame, d2);
        bitwise_xor(d1, d2, result);
        
        int middle_y = result.rows/2;
        int middle_x = result.cols/2;
	int count = 0;
		
        threshold(result, result, 50, 255, CV_THRESH_BINARY);
        Mat kernel_dil = getStructuringElement(MORPH_RECT, Size(7,7));
        erode(result, result, kernel_dil);

	for(int j = middle_y-window+50; j < middle_y+window+100; j++){
            for(int i = 500; i < middle_x+window*3; i++){
                if(result.at<int>(j,i)>0)
                {
                    count++;
                    Point x((i-960)*4-30,j-30);
                    Point y((i-960)*4+30,j+30);
                    rectangle(original,x,y,sc2,1);
                    break;
                }
            }

        }			
        
	 // If a lot of changes happened on multiple rows
	 // we assume something big changed.
        if(count>=30)
            saveImg(original,DIR,EXT,DIR_FORMAT.c_str(),FILE_FORMAT.c_str());
        

        prev_frame = current_frame;
        current_frame = next_frame;
        next_frame = cvQueryFrame(camera);

        cvtColor(next_frame, next_frame, CV_RGB2GRAY);
        
        // Delay
        int key = cvWaitKey (DELAY);

    }
 	
    return 0;    
}
