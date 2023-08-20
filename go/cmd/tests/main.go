package main

import (
	"bufio"
	"fmt"
	"os"
	"strconv"
	"strings"
)

func murmur2(data []byte) int32 {
	length := int32(len(data))
	seed := uint32(0x9747b28c)
	m := int32(0x5bd1e995)
	r := int32(24)

	h := int32(seed ^ uint32(length))
	length4 := length / 4

	for i := int32(0); i < length4; i++ {
		i4 := i * 4
		k := int32(data[i4+0]&0xff) + (int32(data[i4+1]&0xff) << 8) + (int32(data[i4+2]&0xff) << 16) + (int32(data[i4+3]&0xff) << 24)
		k *= m
		k ^= int32(uint32(k) >> r)
		k *= m
		h *= m
		h ^= k
	}

	switch length % 4 {
	case 3:
		h ^= int32(data[(length & ^3)+2]&0xff) << 16
		fallthrough
	case 2:
		h ^= int32(data[(length & ^3)+1]&0xff) << 8
		fallthrough
	case 1:
		h ^= int32(data[length & ^3] & 0xff)
		h *= m
	}

	h ^= int32(uint32(h) >> 13)
	h *= m
	h ^= int32(uint32(h) >> 15)

	return h
}

type Data struct {
	line       string
	hash       int32
	sourceHash int32
}

func main() {
	scanner := bufio.NewScanner(os.Stdin)
	linesDone := 0
	for scanner.Scan() {
		line := scanner.Text()
		parts := strings.Split(line, ",")
		if len(parts) >= 3 {
			inputValue := parts[0]
			hashVal, _ := strconv.Atoi(parts[1])
			goHashVal := murmur2([]byte(inputValue))

			if goHashVal != int32(hashVal) {
				fmt.Printf("hash with input %s does not match source. source=%d, hash=%d\n", inputValue, hashVal, goHashVal)
			}
		}
		linesDone += 1
		if linesDone%100000 == 0 {
			fmt.Printf("done %d lines\n", linesDone)
		}
	}

	if err := scanner.Err(); err != nil {
		fmt.Println("Error reading input:", err)
	}
}
