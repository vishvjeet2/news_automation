from gtts import gTTS
import sys
import os

text = sys.argv[1]

# Get Laravel project root
BASE_DIR = os.getcwd()

# Build correct storage path
output_path = os.path.join(BASE_DIR, "storage", "hindi.mp3")

# Ensure directory exists
os.makedirs(os.path.dirname(output_path), exist_ok=True)

# Generate speech
tts = gTTS(text=text, lang='hi')
tts.save(output_path)
