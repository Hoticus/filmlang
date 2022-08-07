import React from "react"

const PrimaryButton = ({ className = "", ...props }) => {
  return (
    <button
      {...props}
      className={`rounded-lg w-full bg-blue-500 enabled:hover:bg-blue-600 px-4 py-2 text-white ${className}`}
    >
      {props.children}
    </button>
  )
}

export default PrimaryButton
