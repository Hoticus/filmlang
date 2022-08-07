import React from "react"
import Input from "./Input"
import PrimaryButton from "./PrimaryButton"
import Spinner from "./Spinner"
import { Navigate } from "react-router-dom"

class AuthenticationForm extends React.Component {
  state = {
    email: "",
    emailSent: false,
    code: "",
    error: null,
    loading: false,
    resendEmailCounter: 60,
    authenticated: false,
  }

  constructor(props) {
    super(props)

    this.setAuthenticated = props.setAuthenticated

    this.handleCodeChange = this.handleCodeChange.bind(this)
    this.handleEmailChange = this.handleEmailChange.bind(this)
    this.handleUsernameSubmit = this.handleUsernameSubmit.bind(this)
    this.handleSubmit = this.handleSubmit.bind(this)
    this.sendEmail = this.sendEmail.bind(this)
  }

  handleCodeChange(event) {
    this.setState({ code: event.target.value })
  }

  handleEmailChange(event) {
    this.setState({ email: event.target.value })
  }

  sendEmail(resend = false) {
    if (!resend) {
      this.setState({ loading: true })
    }
    const data = new FormData()
    let newError = false
    data.append("_email", this.state.email)
    fetch("/api/authentication/send-email", {
      method: "POST",
      body: data,
    })
      .catch(() => {
        document.location.reload()
      })
      .then((res) => {
        if (!res.ok) {
          newError = true
        }
        return res.json()
      })
      .then((res) => {
        if (newError) {
          this.setState({ error: res.error_message })
        } else {
          this.setState({
            emailSent: true,
            error: null,
            resendEmailCounter: 60,
          })
          const interval = setInterval(() => {
            if (this.state.resendEmailCounter !== 0) {
              this.setState({
                resendEmailCounter: this.state.resendEmailCounter - 1,
              })
            } else {
              clearInterval(interval)
            }
          }, 1000)
        }
        if (!resend) {
          this.setState({ loading: false })
        }
      })
  }

  handleUsernameSubmit(event) {
    this.sendEmail()

    event.preventDefault()
  }

  handleSubmit(event) {
    this.setState({ loading: true })
    const data = new FormData()
    let newError = false
    data.append("username", this.state.email)
    data.append("code", this.state.code)
    fetch("/api/authentication", {
      method: "POST",
      body: data,
    })
      .catch(() => {
        document.location.reload()
      })
      .then((res) => {
        if (!res.ok) {
          newError = true
        }
        return res.json()
      })
      .then((res) => {
        if (newError) {
          this.setState({ error: res.error_message })
        } else {
          this.setState({ error: null, authenticated: true })
          this.setAuthenticated(true)
        }
        this.setState({ loading: false })
      })

    event.preventDefault()
  }

  render() {
    if (this.state.emailSent) {
      return (
        <form
          className="flex flex-col mx-auto container max-w-xl px-4 space-y-5 mt-6"
          onSubmit={this.handleSubmit}
        >
          {this.state.authenticated && (
            <Navigate
              to="/"
              state={{ authenticated: this.state.authenticated }}
            />
          )}

          <h1 className="text-center text-4xl">Authentication</h1>

          <label className="text-center">
            A verification code has been sent to {this.state.email}. Please
            enter the code to authenticate.
            <Input
              required
              id="code"
              value={this.state.code}
              onChange={this.handleCodeChange}
              className="mt-2 text-center"
            />
          </label>
          <PrimaryButton type="submit" disabled={this.state.loading}>
            {this.state.loading ? <Spinner className="w-6 h-6" /> : "Submit"}
          </PrimaryButton>
          <p className="text-center">
            Didn't receive the email?{" "}
            {this.state.resendEmailCounter > 0 ? (
              `Resend in ${this.state.resendEmailCounter}`
            ) : (
              <a
                className="text-blue-500 hover:text-blue-600 underline"
                role="button"
                tabIndex="0"
                onClick={() => this.sendEmail(true)}
              >
                Resend
              </a>
            )}
          </p>
          {this.state.error !== null && (
            <p className="bg-red-50 text-sm text-red-600 border border-red-500 rounded-lg p-2.5 text-center">
              {this.state.error}
            </p>
          )}
        </form>
      )
    } else {
      return (
        <form
          className="flex flex-col mx-auto container max-w-xl px-4 space-y-5 mt-6"
          onSubmit={this.handleUsernameSubmit}
        >
          <h1 className="text-center text-4xl">Authentication</h1>

          <label className="text-center">
            Please enter an email to which we will send you a verification code.
            <Input
              required
              type="email"
              placeholder="example@domain.com"
              id="username"
              value={this.state.email}
              onChange={this.handleEmailChange}
              className="mt-2 text-center"
            />
          </label>

          <PrimaryButton type="submit" disabled={this.state.loading}>
            {this.state.loading ? <Spinner className="w-6 h-6" /> : "Next"}
          </PrimaryButton>
          {this.state.error !== null && (
            <p className="bg-red-50 text-sm text-red-600 border border-red-500 rounded-lg p-2.5 text-center">
              {this.state.error}
            </p>
          )}
        </form>
      )
    }
  }
}

export default AuthenticationForm
