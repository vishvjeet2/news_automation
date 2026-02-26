from gtts import gTTS
import sys

text = sys.argv[1]

tts = gTTS(text=text, lang='hi')
tts.save("D:/xamp/htdocs/laravel/quiz_management/quiz_management/storage/app/public/hindi.mp3")