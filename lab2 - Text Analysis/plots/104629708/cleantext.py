#!/usr/bin/env python

"""Clean comment text for easier parsing."""

from __future__ import print_function

import json
import sys
import re
import string
import argparse
import pickle


__author__ = ""
__email__ = ""

# Some useful data.
_CONTRACTIONS = {
    "tis": "'tis",
    "aint": "ain't",
    "amnt": "amn't",
    "arent": "aren't",
    "cant": "can't",
    "couldve": "could've",
    "couldnt": "couldn't",
    "didnt": "didn't",
    "doesnt": "doesn't",
    "dont": "don't",
    "hadnt": "hadn't",
    "hasnt": "hasn't",
    "havent": "haven't",
    "hed": "he'd",
    "hell": "he'll",
    "hes": "he's",
    "howd": "how'd",
    "howll": "how'll",
    "hows": "how's",
    "id": "i'd",
    "ill": "i'll",
    "im": "i'm",
    "ive": "i've",
    "isnt": "isn't",
    "itd": "it'd",
    "itll": "it'll",
    "its": "it's",
    "mightnt": "mightn't",
    "mightve": "might've",
    "mustnt": "mustn't",
    "mustve": "must've",
    "neednt": "needn't",
    "oclock": "o'clock",
    "ol": "'ol",
    "oughtnt": "oughtn't",
    "shant": "shan't",
    "shed": "she'd",
    "shell": "she'll",
    "shes": "she's",
    "shouldve": "should've",
    "shouldnt": "shouldn't",
    "somebodys": "somebody's",
    "someones": "someone's",
    "somethings": "something's",
    "thatll": "that'll",
    "thats": "that's",
    "thatd": "that'd",
    "thered": "there'd",
    "therere": "there're",
    "theres": "there's",
    "theyd": "they'd",
    "theyll": "they'll",
    "theyre": "they're",
    "theyve": "they've",
    "wasnt": "wasn't",
    "wed": "we'd",
    "wedve": "wed've",
    "well": "we'll",
    "were": "we're",
    "weve": "we've",
    "werent": "weren't",
    "whatd": "what'd",
    "whatll": "what'll",
    "whatre": "what're",
    "whats": "what's",
    "whatve": "what've",
    "whens": "when's",
    "whered": "where'd",
    "wheres": "where's",
    "whereve": "where've",
    "whod": "who'd",
    "whodve": "whod've",
    "wholl": "who'll",
    "whore": "who're",
    "whos": "who's",
    "whove": "who've",
    "whyd": "why'd",
    "whyre": "why're",
    "whys": "why's",
    "wont": "won't",
    "wouldve": "would've",
    "wouldnt": "wouldn't",
    "yall": "y'all",
    "youd": "you'd",
    "youll": "you'll",
    "youre": "you're",
    "youve": "you've"
}

# You may need to write regular expressions.

def sanitize(text):
    """Do parse the text in variable "text" according to the spec, and return
    a LIST containing FOUR strings 
    1. The parsed text.
    2. The unigrams
    3. The bigrams
    4. The trigrams
    """
    # YOUR CODE GOES BELOW:
    lowertext = text.lower()
    lowertext.replace('\n', ' ')
    lowertext.replace('\t', ' ')
    #url_re = re.compile('\((http.*?)\)')
    #url = re.findall(url_re, lowertext)
    #for x in url:
    #	lowertext = lowertext.replace(x, '')
    lowertext = re.sub(r'([\w])(\.\.\.\.\.)([\w])', r'\1ååååå\3', lowertext)
    lowertext = re.sub(r'([\w])(\.\.\.\.)([\w])', r'\1åååå\3', lowertext)
    lowertext = re.sub(r'([\w])(\.\.\.)([\w])', r'\1ååå\3', lowertext)
    lowertext = re.sub(r'\(?https?:/(/.*)*\)?', '', lowertext)
    lowertext = re.sub('\s\s*', ' ', lowertext)
    lowertext = re.sub(r'([^\w])([,.!?:;])()', r'\1 \2 \3', lowertext)
    lowertext = re.sub(r'()([,.!?:;])([^\w])', r'\1 \2 \3', lowertext)
    lowertext = re.sub(r'(\A[,.!?:;])', r'\1 ', lowertext)
    lowertext = re.sub(r'([,.!?:;]\Z)', r' \1', lowertext)

    lowertext = re.sub(r'([^\w])([^\w,.!?;:$%\'\s])()', r'\1 \3', lowertext)
    lowertext = re.sub(r'()([^\w,.!?;:$%\'\s])([^\w])', r'\1 \3', lowertext)
    lowertext = re.sub(r'(\A[^\w,.!?;:$%\'])', r'', lowertext)
    lowertext = re.sub(r'([^\w,.!?;:$%\']\Z)', r'', lowertext)
    lowertext = lowertext.replace(' "', ' ')
    lowertext = lowertext.replace('" ', ' ')

    lowertext = re.sub(r'([^\w])([^\w,.!?;:$%\'\s])()', r'\1 \3', lowertext)
    lowertext = re.sub(r'()([^\w,.!?;:$%\'\s])([^\w])', r'\1 \3', lowertext)
    lowertext = re.sub(r'(\A[^\w,.!?;:$%\'])', r'', lowertext)
    lowertext = re.sub(r'([^\w,.!?;:$%\']\Z)', r'', lowertext)
    lowertext = lowertext.replace(' "', ' ')
    lowertext = lowertext.replace('" ', ' ')
    #lowertext = lowertext.replace(' \'', ' ')
    #lowertext = lowertext.replace('\' ', ' ')
    lowertext = re.sub('\s\s*', ' ', lowertext)
    lowertext = lowertext.replace(r"\Atis ", "'tis ")
    #lowertext = lowertext.replace(r"\Aol ", "'ol ")
    lowertext = lowertext.replace(" tis ", "'tis ")
    #lowertext = lowertext.replace(" ol ", "'ol ")
    lowertext = lowertext.replace(r" tis\Z", " 'tis")
    #lowertext = lowertext.replace(r" ol\Z", " 'ol")
    #print(lowertext)
    lowertext = lowertext.replace('ååååå', '.....')
    lowertext = lowertext.replace('åååå', '....')
    lowertext = lowertext.replace('ååå', '...')
    #lowertext = re.sub(r'( \. \. \. \. \.)([^\s])', r'.....\2', lowertext)
    #print(lowertext)


    lowertext = re.sub('\s\s*', ' ', lowertext)
    parsed_text = lowertext.strip()
    #print(parsed_text)

    unigrams = re.sub(' [^\w$%\'\s]', '', lowertext)
    unigrams = re.sub('\A[^\w$%\'\s] ', '', unigrams).strip()

    bigrams_list = re.split(' [^\w$%\'\s]', lowertext)
    bigrams_list[0] = re.sub('\A[^\w$%\'\s] ', '', bigrams_list[0])
    bigrams = ""
    trigrams = ""
    for x in bigrams_list:
    	x = x.strip()
    	words = x.split(' ')
    	for i in range(0, len(words)-1):
    		bigram = words[i]+'_'+words[i+1]
    		bigrams += bigram
    		bigrams += ' '
    	for i in range(0, len(words)-2):
    		trigram = words[i]+'_'+words[i+1]+'_'+words[i+2]
    		trigrams += trigram
    		trigrams += ' '
    bigrams = bigrams.strip()
    trigrams = trigrams.strip()

    return [parsed_text, unigrams, bigrams, trigrams]


if __name__ == "__main__":
    # This is the Python main function.
    # You should be able to run
    # python cleantext.py <filename>
    # and this "main" function will open the file,
    # read it line by line, extract the proper value from the JSON,
    # pass to "sanitize" and print the result as a list.

    # YOUR CODE GOES BELOW.
    if len(sys.argv) < 2:
        print ("please specifiy the filename.")
        sys.exit()
    fname = sys.argv[1]
    # parsed = open('parsed.txt','w')
    # unigrams = open('unigrams.txt','w')
    # bigrams = open('bigrams.txt','w')
    # trigrams = open('trigrams.txt','w')

    data = []
    texts = []
    with open(fname) as f:
        #output = open("out.txt", 'w')
        for line in f:
            data.append(json.loads(line))
        for obj in data:
            texts.append(obj['body'])
        for s in texts:
            #print (sanitize(s))
            new = sanitize(s)
            # parsed.write(new[0])
            # parsed.write('\n')
            # unigrams.write(new[1])
            # unigrams.write('\n')
            # bigrams.write(new[2])
            # bigrams.write('\n')
            # trigrams.write(new[3])
            # trigrams.write('\n')
            print(new)
    # with open(fname) as f:
    #     strings = f.readlines()
    #     for s in strings:
    #         print (s)
    #         parsed = sanitize(s)[0]
    #         unigrams = sanitize(s)[1]
    #         bigrams = sanitize(s)[2]
    #         trigrams = sanitize(s)[3]
    #         print (parsed)
    #         print (unigrams)
    #         print (bigrams)
    #         print (trigrams)










