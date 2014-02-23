//
//  Created by Cedric Verstraeten on 22/02/14.
//  Copyright (c) 2014 Cedric Verstraeten. All rights reserved.
//

#include <iostream>
#include <vector>
#include <opencv2/opencv.hpp>
#include "tinyxml.h"
using namespace cv;
using namespace std;

Mat src;
string filename, xml;
Scalar color(0,0,255); // red color
vector<Point2f> coor;
int coor_num = 0;

void savePointsAsXML(vector<Point2f> & contour){
    TiXmlDocument doc;
    TiXmlDeclaration decl("1.0", "", "");
    doc.InsertEndChild(decl);
    for(int i = 0; i < contour.size(); i++)
    {
        TiXmlElement point("point");
        point.SetAttribute("x",contour[i].x);
        point.SetAttribute("y",contour[i].y);
        doc.InsertEndChild(point);
    }
    if(doc.SaveFile(xml.c_str()))
        cout << "file saved succesfully.\n";
    else
        cout << "file not saved, something went wrong!\n";
}

void getPointsInContour(vector<Point2f> & contour){
    vector<Point2f> insideContour;
    for(int j = 0; j < src.rows; j++){
        for(int i = 0; i < src.cols; i++){
            Point2f p(i,j);
            if(pointPolygonTest(contour,p,false) >= 0) // yes inside
                insideContour.push_back(p);
        }
    }
    cout << "# points inside contour: " << insideContour.size() << endl;
    savePointsAsXML(insideContour);
}

void onMouse(int event, int x, int y, int, void* ) {
    if (event == CV_EVENT_LBUTTONUP) {
        Point2f p(x, y);
        coor.push_back(p);
        line(src,p,p,color);
        if(coor.size()>1)
            line(src, p, coor[coor.size()-2], color);
        imshow("imageWindow", src);
    }
    else if (event == CV_EVENT_RBUTTONUP && coor.size()>2){
        line(src, coor[0], coor[coor.size()-1], color);
        getPointsInContour(coor);
        imshow("imageWindow", src);
        waitKey(2000);
        exit(0);
    }
}

int main(int argc, char **argv)
{
    if(argc==3){
        filename = argv[1];
        xml = argv[2];
    }
    else{
        printf("specify arguments: path to image [0], output xml file [1]\n");
        exit(1);
    }
    // The image that will be analyzed
    src = imread(filename, -1);
    printf("image openend.\n");
    namedWindow("imageWindow", CV_WINDOW_AUTOSIZE );
    imshow("imageWindow", src);
    setMouseCallback( "imageWindow", onMouse, 0 );
    waitKey(0);
	return 0;
}