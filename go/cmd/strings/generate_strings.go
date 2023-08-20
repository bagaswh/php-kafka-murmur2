package main

import (
	"fmt"
	"math/rand"
	"runtime"
	"sync"
	"time"
)

const (
	numStrings   = 5000000
	stringLength = 16
)

var numGoroutines = runtime.NumCPU()

const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%^&*()-=_+[]\\{}|;':\"./?><"

func generateRandomString(length int) string {
	result := make([]byte, length)
	for i := range result {
		result[i] = charset[rand.Intn(len(charset))]
	}
	return string(result)
}

func doGeneraterandomStrings(numStrings int, stringLength int, wg *sync.WaitGroup, ch chan<- string) {
	defer wg.Done()
	for i := 0; i < numStrings; i++ {
		randomString := generateRandomString(stringLength)
		ch <- randomString
	}
}

func main() {
	rand.Seed(time.Now().UnixNano())

	runtime.GOMAXPROCS(numGoroutines)

	var wg sync.WaitGroup
	ch := make(chan string, numGoroutines)

	for i := 0; i < numGoroutines; i++ {
		wg.Add(1)
		go doGeneraterandomStrings(numStrings/numGoroutines, stringLength, &wg, ch)
	}

	go func() {
		wg.Wait()
		close(ch)
	}()

	for randomString := range ch {
		fmt.Println(randomString)
	}
}
