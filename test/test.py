from client import send
import threading
from concurrent.futures import ThreadPoolExecutor
import time

def test(url: str, frequency: int) -> None:
    for _ in range(frequency):
        send(url)

def main() -> None:
    try:
        concurrency = int(input('Concurrency (k): ')) * 1000
        frequency = int(input('Frequency (k): ')) * 1000
        url = input('URL: ')

        start_time = time.time()

        with ThreadPoolExecutor(max_workers=concurrency) as executor:
            futures = []
            for _ in range(concurrency):
                futures.append(executor.submit(test, url, frequency // concurrency))

            for future in futures:
                future.result()

        end_time = time.time()
        
        total_time = end_time - start_time
        print(f"All tasks completed in {total_time:.2f} seconds.")
    except ValueError:
        print("Please enter valid integer values for concurrency and frequency.")

if __name__ == "__main__":
    main()
