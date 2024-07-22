import urllib.request

def send(url:str) -> str:
    try:
        body = urllib.request.urlopen(url)
        return body.read()
    except Exception as e:
        return str(e)
