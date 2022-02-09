# MakeImgPPT

현재 폴더에 있는 사진들을 크기를 줄이고, 파워포인트에 넣어준다

## 사전 설정
1. php 설치 (<a href="./php-7.0.5-Win32-VC14-x64.zip">다운로드</a>)
  C:\php7\ 에 php를 설치한다.

2. php.ini 세팅 : 아래의 extension을 사용하도록 한다(uncomment)
	extension=mbstring
	extension=exif
	extension=gd2

3. POWERPOINT 경로 확인 및 batch 파일 수정

## 사용법
1. bat 파일을 사진이 있는 폴더로 가져간다.

2. bat 파일을 실행시키면, 사진 크기를 조정하여 rs 폴더에 복사된 후, 파워포인트가 자동 실행되며, rs_1.htm 파일이 열린다.

3. 파워포인트에서 그림파일들에 대한 파일 연결을 끊는다. 연결 설정이 끊어져야 ppt내에 사진이 저장된다. 
  - 준비>파일연결편집
  - (연결 창에서) 연결 모두 선택
  - (연결 창에서) 연결 끊기
  - (연결 창에서) 닫기

4. 다른 이름으로 저장한다.(파일형식 : pptx)

5. rs_1.htm 삭제 (이때 자동으로 rs_1.files 폴더도 함께 삭제됨)

## Image Resize (AutoHotKey)
1. 사진 조정을 위한 파워포인트를 연 후에, AutoHotKey 파일(<a href="./ImgResize.ahk">ImgResize.ahk</a>) 실행

2. 파워포인트에서 사진을 선택한 후, 단축키 누르면 사진 크기 변경됨
  - F1 : 가로 폭 100%
  - F2 : 가로 폭 66% (=2/3)
  - F3 : 가로 폭 50% (=1/2)
  - F4 : 가로 폭 33% (=1/3)
  - F5 : 세로 폭 100%
  - F6 : 세로 폭 66% (=2/3)
  - F7 : 세로 폭 50% (=1/2)
  - F8 : 세로 폭 33% (=1/3)
  - Ctrl+F1 : 여러 사진 왼쪽 맞춤
  - Ctrl+F2 : 여러 사진 세로가운데 맞춤
  - Ctrl+F3 : 여러 사진 오른쪽 맞춤
  - Ctrl+F5 : 여러 사진 윗쪽 맞춤
  - Ctrl+F6 : 여러 사진 가로가운데 맞춤
  - Ctrl+F7 : 여러 사진 아래 맞춤
  - F10 : AutoHotKey Exit

