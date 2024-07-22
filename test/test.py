from client import send
import threading
from concurrent.futures import ThreadPoolExecutor

def test(url: str, frequency: int) -> None:
    for _ in range(frequency):
        send(url)

def main() -> None:
    try:
        concurrency = int(input('Concurrency (k): ')) * 1000
        frequency = int(input('Frequency (k): ')) * 1000
        url = input('URL: ')
        
        with ThreadPoolExecutor(max_workers=concurrency) as executor:
            for _ in range(concurrency):
                executor.submit(test, url, frequency // concurrency)
        
        print("All tasks completed.")
    except ValueError:
        print("Please enter valid integer values for concurrency and frequency.")

if __name__ == "__main__":
    main()
