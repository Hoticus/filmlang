import React from "react"

const Input = ({ type = "text", className = "", ...props }) => {
  return (
    <input
      type={type}
      {...props}
      className={`rounded-md w-full border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 ${className}`}
    />
  )
}

export default Input
